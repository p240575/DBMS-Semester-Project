<?php
require_once 'controllers/Controller.php';

class CartController extends Controller {
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $product_id = (int)$_POST['product_id'];
            $quantity   = max(1, (int)($_POST['quantity'] ?? 1));
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            header("Location: /cart"); exit;
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $product_id = (int)$_POST['product_id'];
            $action     = $_POST['action'] ?? '';
            if ($action === 'increase') {
                $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
            } elseif ($action === 'decrease') {
                if (isset($_SESSION['cart'][$product_id]) && $_SESSION['cart'][$product_id] > 1) {
                    $_SESSION['cart'][$product_id]--;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
            } elseif ($action === 'remove') {
                unset($_SESSION['cart'][$product_id]);
            }
            header("Location: /cart"); exit;
        }
    }

    public function clear() {
        unset($_SESSION['cart'], $_SESSION['discount']);
        header("Location: /cart"); exit;
    }

    public function promo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['promo_code'])) {
            require_once 'config/database.php';
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("SELECT * FROM Coupons WHERE code = ? AND is_active = 1");
            $stmt->execute([strtoupper(trim($_POST['promo_code']))]);
            $coupon = $stmt->fetch();
            if ($coupon) {
                $_SESSION['discount'] = $coupon['discount_percent'];
                $_SESSION['promo_success'] = "Promo code applied! " . $coupon['discount_percent'] . "% off.";
            } else {
                $_SESSION['promo_error'] = "Invalid or expired promo code.";
            }
            header("Location: /cart"); exit;
        }
    }

    public function index() {
        require_once 'config/database.php';
        $db = (new Database())->getConnection();
        $cartItems = [];
        $total = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $stmt = $db->prepare("SELECT p.product_id, p.name, pv.price, pv.variant_key, pv.stock, pi.image_url FROM Products p LEFT JOIN ProductVariants pv ON p.product_id = pv.product_id LEFT JOIN ProductImages pi ON p.product_id = pi.product_id AND pi.is_default = 1 WHERE p.product_id = ? LIMIT 1");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($product) {
                    $product['cart_quantity'] = $quantity;
                    $cartItems[] = $product;
                    $total += $product['price'] * $quantity;
                }
            }
        }
        // Wallet balance
        $walletBalance = 0;
        if (isset($_SESSION['user_id'])) {
            $sw = $db->prepare("SELECT wallet_balance FROM Users WHERE user_id = ?");
            $sw->execute([$_SESSION['user_id']]);
            $wu = $sw->fetch(PDO::FETCH_ASSOC);
            $walletBalance = $wu['wallet_balance'] ?? 0;
        }
        $this->view('cart/index', ['cartItems' => $cartItems, 'total' => $total, 'wallet_balance' => $walletBalance]);
    }
}
?>
