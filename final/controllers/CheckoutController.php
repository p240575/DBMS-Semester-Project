<?php
require_once 'controllers/Controller.php';

class CheckoutController extends Controller {
    public function index() {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        if (empty($_SESSION['cart']))      { header("Location: /cart"); exit; }

        require_once 'config/database.php';
        $db = (new Database())->getConnection();

        $stmt = $db->prepare("SELECT * FROM Addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $swallet = $db->prepare("SELECT wallet_balance FROM Users WHERE user_id = ?");
        $swallet->execute([$_SESSION['user_id']]);
        $wu = $swallet->fetch(PDO::FETCH_ASSOC);
        $walletBalance = $wu['wallet_balance'] ?? 0;

        $cartItems = [];
        $total = 0;
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $sv = $db->prepare("SELECT p.product_id, p.name, pv.price, pv.variant_key, pi.image_url FROM Products p LEFT JOIN ProductVariants pv ON p.product_id = pv.product_id LEFT JOIN ProductImages pi ON p.product_id = pi.product_id AND pi.is_default = 1 WHERE p.product_id = ? LIMIT 1");
            $sv->execute([$product_id]);
            $product = $sv->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                $product['cart_quantity'] = $quantity;
                $cartItems[] = $product;
                $total += $product['price'] * $quantity;
            }
        }

        $discount = $_SESSION['discount'] ?? 0;
        $afterDiscount = $total * (1 - $discount / 100);

        $this->view('checkout/index', [
            'cartItems'     => $cartItems,
            'total'         => $afterDiscount,
            'raw_total'     => $total,
            'discount'      => $discount,
            'addresses'     => $addresses,
            'wallet_balance' => $walletBalance
        ]);
    }

    public function process() {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
            header("Location: /home"); exit;
        }

        require_once 'config/database.php';
        $db = (new Database())->getConnection();

        try {
            $db->beginTransaction();

            // Recalculate total
            $total = 0;
            $cartItems = [];
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $sv = $db->prepare("SELECT p.product_id, p.name, pv.price, pv.variant_id, pv.variant_key FROM Products p LEFT JOIN ProductVariants pv ON p.product_id = pv.product_id WHERE p.product_id = ? LIMIT 1");
                $sv->execute([$product_id]);
                $product = $sv->fetch(PDO::FETCH_ASSOC);
                if ($product) {
                    $product['cart_quantity'] = $quantity;
                    $cartItems[] = $product;
                    $total += $product['price'] * $quantity;
                }
            }
            $discount = $_SESSION['discount'] ?? 0;
            $afterDiscount = $total * (1 - $discount / 100);

            // Payment method
            $paymentMethod = $_POST['payment_method'] ?? 'cod';
            $swallet = $db->prepare("SELECT wallet_balance FROM Users WHERE user_id = ?");
            $swallet->execute([$_SESSION['user_id']]);
            $wu = $swallet->fetch(PDO::FETCH_ASSOC);
            $walletBalance = $wu['wallet_balance'] ?? 0;

            $walletUsed = 0;
            $codAmount  = $afterDiscount;

            if ($paymentMethod === 'wallet' || $paymentMethod === 'wallet_cod') {
                $walletUsed = min($walletBalance, $afterDiscount);
                $codAmount  = max(0, $afterDiscount - $walletUsed);
                // Deduct from wallet
                if ($walletUsed > 0) {
                    $db->prepare("UPDATE Users SET wallet_balance = wallet_balance - ? WHERE user_id = ?")
                       ->execute([$walletUsed, $_SESSION['user_id']]);
                }
            }

            // Payment label for order
            if ($paymentMethod === 'wallet' && $codAmount == 0) {
                $payLabel = 'Wallet';
            } elseif ($walletUsed > 0) {
                $payLabel = 'Wallet + COD';
            } else {
                $payLabel = 'Cash on Delivery';
            }

            // Create order
            $initialTotal = $afterDiscount - $total;
            $stmt = $db->prepare("INSERT INTO Orders (user_id, total_amount, order_status, payment_method, wallet_amount) VALUES (?, ?, 'pending', ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $initialTotal, $payLabel, $walletUsed]);
            $order_id = $db->lastInsertId();

            // Order items
            foreach ($cartItems as $item) {
                $stmtItem = $db->prepare("INSERT INTO OrderItems (order_id, variant_id, product_name, variant_label, quantity, price) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtItem->execute([$order_id, $item['variant_id'], $item['name'], $item['variant_key'] ?? 'Default', $item['cart_quantity'], $item['price']]);
                // Decrease stock
                $db->prepare("UPDATE ProductVariants SET stock = stock - ? WHERE variant_id = ?")->execute([$item['cart_quantity'], $item['variant_id']]);
            }

            // Address
            $address_id = $_POST['address_id'] ?? null;
            if ($address_id) {
                $stmtAddr = $db->prepare("SELECT * FROM Addresses WHERE address_id = ? AND user_id = ? LIMIT 1");
                $stmtAddr->execute([$address_id, $_SESSION['user_id']]);
            } else {
                $stmtAddr = $db->prepare("SELECT * FROM Addresses WHERE user_id = ? LIMIT 1");
                $stmtAddr->execute([$_SESSION['user_id']]);
            }
            $addr = $stmtAddr->fetch();
            if ($addr) {
                $db->prepare("INSERT INTO OrderAddresses (order_id, full_name, phone, address_line, city, province, zipcode) VALUES (?, ?, ?, ?, ?, ?, ?)")
                   ->execute([$order_id, $addr['full_name'], $addr['phone'], $addr['address_line'], $addr['city'], $addr['province'], $addr['zipcode']]);
            }

            // Notification
            $walletMsg = $walletUsed > 0 ? " Rs ".number_format($walletUsed,2)." paid from wallet." : "";
            $codMsg    = $codAmount  > 0 ? " Rs ".number_format($codAmount,2)." to be paid on delivery." : "";
            $db->prepare("INSERT INTO Notifications (user_id, message) VALUES (?, ?)")
               ->execute([$_SESSION['user_id'], "✅ Order #$order_id placed! Total: Rs ".number_format($afterDiscount,2).".{$walletMsg}{$codMsg}"]);

            $db->commit();
            unset($_SESSION['cart'], $_SESSION['discount']);
            header("Location: /checkout/success"); exit;

        } catch (Exception $e) {
            $db->rollBack();
            die("Checkout failed: " . $e->getMessage());
        }
    }

    public function success() {
        $this->view('checkout/success');
    }
}
?>
