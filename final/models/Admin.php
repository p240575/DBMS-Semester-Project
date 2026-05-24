<?php
class Admin {
    private $conn;

    public function __construct() {
        require_once 'config/database.php';
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getDashboardStats() {
        $stats = [];
        
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM Users WHERE role = 'customer'");
        $stmt->execute(); $stats['total_customers'] = $stmt->fetch()['count'];

        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM Orders");
        $stmt->execute(); $stats['total_orders'] = $stmt->fetch()['count'];

        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM Orders WHERE order_status IN ('pending','confirmed','shipped')");
        $stmt->execute(); $stats['pending_orders'] = $stmt->fetch()['count'];

        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM Orders WHERE order_status IN ('delivered','cancelled')");
        $stmt->execute(); $stats['completed_orders'] = $stmt->fetch()['count'];

        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM Coupons");
        $stmt->execute(); $stats['total_coupons'] = $stmt->fetch()['count'];

        // Revenue = delivered orders ONLY minus refunded amounts
        $stmt = $this->conn->prepare("SELECT COALESCE(SUM(total_amount),0) as total FROM Orders WHERE order_status = 'delivered'");
        $stmt->execute(); $gross = $stmt->fetch()['total'];
        $stmt2 = $this->conn->prepare("SELECT COALESCE(SUM(refund_amount),0) as total FROM ReturnRequests WHERE status = 'refunded'");
        $stmt2->execute(); $refunded = $stmt2->fetch()['total'];
        $stats['total_revenue'] = $gross - $refunded;
        $stats['gross_revenue'] = $gross;
        $stats['total_refunded'] = $refunded;

        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM Products");
        $stmt->execute(); $stats['total_products'] = $stmt->fetch()['count'];

        $stmt = $this->conn->prepare("SELECT COUNT(DISTINCT p.product_id) as count FROM Products p JOIN ProductVariants pv ON p.product_id = pv.product_id WHERE pv.stock <= 5");
        $stmt->execute(); $stats['low_stock_products'] = $stmt->fetch()['count'];

        // Return requests count
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM ReturnRequests WHERE status IN ('pending','approved','returning','admin_received')");
        $stmt->execute(); $stats['pending_returns'] = $stmt->fetch()['count'];

        return $stats;
    }

    public function getOrders() {
        $stmt = $this->conn->prepare("SELECT o.*, u.user_name, u.email FROM Orders o JOIN Users u ON o.user_id = u.user_id ORDER BY o.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function dispatchOrderWithAgent($order_id, $agent) {
        $stmtCheck = $this->conn->prepare("SELECT created_at, user_id FROM Orders WHERE order_id = :id");
        $stmtCheck->execute(['id' => $order_id]);
        $order = $stmtCheck->fetch();
        if (!$order) return false;
        if ((time() - strtotime($order['created_at'])) < 3600) return false;

        $stmt = $this->conn->prepare("UPDATE Orders SET order_status='shipped', delivery_agent=:agent, dispatched_at=NOW() WHERE order_id=:id");
        $stmt->execute(['agent' => $agent, 'id' => $order_id]);

        $msg = "📦 Your order #$order_id has been dispatched! Agent: $agent. Expected in 3-5 business days. You can confirm receipt after 2 days.";
        $this->addNotification($order['user_id'], $msg);
        return true;
    }

    public function markOrderDelivered($order_id, $user_id) {
        $stmt = $this->conn->prepare("UPDATE Orders SET order_status='delivered', delivered_at=NOW() WHERE order_id=:id AND user_id=:uid AND order_status='shipped'");
        return $stmt->execute(['id' => $order_id, 'uid' => $user_id]);
    }

    public function autoDeliverOldOrders() {
        // Auto-deliver shipped orders older than 72 hours (3 days = 259200 seconds)
        $stmt = $this->conn->prepare("
            UPDATE Orders SET order_status='delivered', delivered_at=NOW()
            WHERE order_status='shipped'
            AND TIMESTAMPDIFF(SECOND, COALESCE(dispatched_at, created_at), NOW()) >= 259200
        ");
        $stmt->execute();

        // Notify users of auto-delivery
        $stmtN = $this->conn->query("
            SELECT o.order_id, o.user_id FROM Orders o
            WHERE order_status='delivered' AND delivered_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
            AND order_id NOT IN (SELECT order_id FROM Notifications WHERE message LIKE '%auto-delivered%')
        ");
        if ($stmtN) {
            foreach ($stmtN->fetchAll() as $o) {
                $this->addNotification($o['user_id'], "✅ Your order #" . $o['order_id'] . " has been auto-delivered after 3 days. You have 2 days to request a return if needed.");
            }
        }
    }

    public function getCustomers() {
        $stmt = $this->conn->prepare("SELECT u.user_id, u.user_name, u.email, u.phone, u.registered_at, u.wallet_balance FROM Users u WHERE u.role = 'customer' ORDER BY u.registered_at DESC");
        $stmt->execute();
        $customers = $stmt->fetchAll();
        foreach ($customers as &$c) {
            $s = $this->conn->prepare("SELECT * FROM Addresses WHERE user_id = :uid ORDER BY is_default DESC, created_at DESC");
            $s->execute(['uid' => $c['user_id']]);
            $c['addresses'] = $s->fetchAll();
        }
        return $customers;
    }

    public function getCoupons() {
        $stmt = $this->conn->prepare("SELECT * FROM Coupons ORDER BY coupon_id DESC");
        $stmt->execute(); return $stmt->fetchAll();
    }
    public function addCoupon($code, $pct) {
        $stmt = $this->conn->prepare("INSERT INTO Coupons (code, discount_percent) VALUES (:c,:p)");
        return $stmt->execute(['c' => $code, 'p' => $pct]);
    }
    public function deleteCoupon($id) {
        $stmt = $this->conn->prepare("DELETE FROM Coupons WHERE coupon_id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getAllProducts() {
        $stmt = $this->conn->prepare("SELECT p.*, pv.price, pv.stock, pv.variant_id, pv.variant_key, c.name as category_name, c.category_id FROM Products p LEFT JOIN ProductVariants pv ON p.product_id = pv.product_id LEFT JOIN ProductCategories pc ON p.product_id = pc.product_id LEFT JOIN Categories c ON pc.category_id = c.category_id GROUP BY p.product_id, pv.variant_id ORDER BY p.product_id DESC");
        $stmt->execute(); return $stmt->fetchAll();
    }
    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM Products WHERE product_id = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function updateProduct($id, $name, $desc, $price, $stock, $variant_id, $vk, $img = null, $nvk_arr = [], $nvp_arr = [], $nvs_arr = []) {
        try {
            $this->conn->beginTransaction();
            $this->conn->prepare("UPDATE Products SET name=:n, description=:d WHERE product_id=:id")->execute(['n'=>$name,'d'=>$desc,'id'=>$id]);
            if ($variant_id) {
                $this->conn->prepare("UPDATE ProductVariants SET price=:p, stock=:s, variant_key=:vk WHERE variant_id=:vid")->execute(['p'=>$price,'s'=>$stock,'vk'=>$vk,'vid'=>$variant_id]);
            }
            if ($img) {
                $u = $this->conn->prepare("UPDATE ProductImages SET image_url=:img WHERE product_id=:pid AND is_default=1");
                $u->execute(['img'=>$img,'pid'=>$id]);
                if ($u->rowCount() === 0) $this->conn->prepare("INSERT INTO ProductImages (product_id,image_url,is_default) VALUES (:pid,:img,1)")->execute(['pid'=>$id,'img'=>$img]);
            }
            // Add new variants arrays
            if (is_array($nvk_arr)) {
                for ($i = 0; $i < count($nvk_arr); $i++) {
                    $nvk = $nvk_arr[$i];
                    $nvp = $nvp_arr[$i];
                    $nvs = $nvs_arr[$i];
                    if (!empty($nvk) && !empty($nvp)) {
                        $this->conn->prepare("INSERT INTO ProductVariants (product_id,price,stock,variant_key) VALUES (:pid,:p,:s,:vk)")
                                   ->execute(['pid'=>$id,'p'=>$nvp,'s'=>$nvs??0,'vk'=>$nvk]);
                    }
                }
            }
            $this->conn->commit(); return true;
        } catch(PDOException $e) { $this->conn->rollBack(); return false; }
    }
    public function addProduct($name, $desc, $cat_id, $img = '/assets/img/hero_1.png', $vk_arr = ['Default'], $price_arr = [0], $stock_arr = [0]) {
        try {
            $this->conn->beginTransaction();
            $this->conn->prepare("INSERT INTO Products (name,description,status) VALUES (:n,:d,'active')")->execute(['n'=>$name,'d'=>$desc]);
            $pid = $this->conn->lastInsertId();
            $this->conn->prepare("INSERT INTO ProductCategories (product_id,category_id) VALUES (:pid,:cid)")->execute(['pid'=>$pid,'cid'=>$cat_id]);
            
            if (is_array($vk_arr)) {
                for ($i = 0; $i < count($vk_arr); $i++) {
                    $vk = !empty($vk_arr[$i]) ? $vk_arr[$i] : 'Default';
                    $price = !empty($price_arr[$i]) ? (float)$price_arr[$i] : 0.00;
                    $stock = !empty($stock_arr[$i]) ? (int)$stock_arr[$i] : 0;
                    
                    $this->conn->prepare("INSERT INTO ProductVariants (product_id,price,stock,variant_key) VALUES (:pid,:p,:s,:vk)")
                               ->execute(['pid'=>$pid,'p'=>$price,'s'=>$stock,'vk'=>$vk]);
                }
            }
            
            $this->conn->prepare("INSERT INTO ProductImages (product_id,image_url,is_default) VALUES (:pid,:img,1)")->execute(['pid'=>$pid,'img'=>$img]);
            $this->conn->commit(); return true;
        } catch(PDOException $e) { $this->conn->rollBack(); return false; }
    }
    public function getAllCategories() {
        $stmt = $this->conn->prepare("SELECT * FROM Categories");
        $stmt->execute(); return $stmt->fetchAll();
    }

    public function deleteCustomer($id) {
        $stmt = $this->conn->prepare("DELETE FROM Users WHERE user_id=:id AND role='customer'");
        return $stmt->execute(['id'=>$id]);
    }
    public function sendNotice($id, $message) {
        return $this->addNotification($id, $message);
    }

    public function getReviews() {
        $stmt = $this->conn->prepare("SELECT r.*, p.name as product_name FROM Reviews r JOIN Products p ON r.product_id = p.product_id ORDER BY r.created_at DESC");
        $stmt->execute(); return $stmt->fetchAll();
    }
    public function deleteReview($id) {
        $stmt = $this->conn->prepare("DELETE FROM Reviews WHERE review_id=:id");
        return $stmt->execute(['id'=>$id]);
    }
    public function replyReview($id, $reply) {
        $stmt = $this->conn->prepare("UPDATE Reviews SET reply=:r WHERE review_id=:id");
        return $stmt->execute(['r'=>$reply,'id'=>$id]);
    }

    // ===== RETURN & REFUND SYSTEM =====

    public function getReturnRequests() {
        $stmt = $this->conn->prepare("
            SELECT r.*, o.total_amount, o.order_status, o.delivered_at, u.user_name, u.email
            FROM ReturnRequests r
            JOIN Orders o ON r.order_id = o.order_id
            JOIN Users u ON r.user_id = u.user_id
            ORDER BY FIELD(r.status,'admin_received','returning','pending','approved','refunded','rejected'), r.created_at DESC
        ");
        $stmt->execute(); return $stmt->fetchAll();
    }

    public function approveReturn($return_id, $return_address, $admin_note = '') {
        $stmt = $this->conn->prepare("SELECT user_id, order_id FROM ReturnRequests WHERE return_id = :id");
        $stmt->execute(['id' => $return_id]);
        $ret = $stmt->fetch();
        
        $this->conn->prepare("UPDATE ReturnRequests SET status='approved', return_address=:addr, admin_note=:note WHERE return_id=:id")
            ->execute(['addr' => $return_address, 'note' => $admin_note, 'id' => $return_id]);
        
        if ($ret) {
            $this->addNotification($ret['user_id'], "✅ Your return request for Order #" . $ret['order_id'] . " has been approved! Please ship the product to: $return_address");
        }
        return true;
    }

    public function markReturnReceived($return_id) {
        $stmt = $this->conn->prepare("SELECT user_id, order_id FROM ReturnRequests WHERE return_id = :id");
        $stmt->execute(['id' => $return_id]);
        $ret = $stmt->fetch();
        
        $this->conn->prepare("UPDATE ReturnRequests SET status='admin_received' WHERE return_id=:id")->execute(['id' => $return_id]);
        
        if ($ret) {
            $this->addNotification($ret['user_id'], "📬 We've received your returned product for Order #" . $ret['order_id'] . ". Our team is inspecting it. You'll be notified shortly.");
        }
    }

    public function processRefund($return_id, $refund_amount, $admin_note = '') {
        $stmt = $this->conn->prepare("SELECT user_id, order_id FROM ReturnRequests WHERE return_id = :id AND status = 'admin_received'");
        $stmt->execute(['id' => $return_id]);
        $ret = $stmt->fetch();
        if (!$ret) return false;

        // Update return request
        $this->conn->prepare("UPDATE ReturnRequests SET status='refunded', refund_amount=:amt, admin_note=:note WHERE return_id=:id")
            ->execute(['amt' => $refund_amount, 'note' => $admin_note, 'id' => $return_id]);

        // Credit wallet
        $this->conn->prepare("UPDATE Users SET wallet_balance = wallet_balance + :amt WHERE user_id = :uid")
            ->execute(['amt' => $refund_amount, 'uid' => $ret['user_id']]);

        $this->addNotification($ret['user_id'], "💰 Refund of Rs " . number_format($refund_amount, 2) . " for Order #" . $ret['order_id'] . " has been added to your NexShop Wallet!");
        return true;
    }

    public function rejectReturn($return_id, $admin_note = '') {
        $stmt = $this->conn->prepare("SELECT user_id, order_id FROM ReturnRequests WHERE return_id = :id");
        $stmt->execute(['id' => $return_id]);
        $ret = $stmt->fetch();
        
        $this->conn->prepare("UPDATE ReturnRequests SET status='rejected', admin_note=:note WHERE return_id=:id")
            ->execute(['note' => $admin_note, 'id' => $return_id]);
        
        if ($ret) {
            $reason = $admin_note ?: 'Product condition did not meet return policy requirements.';
            $this->addNotification($ret['user_id'], "❌ Your return request for Order #" . $ret['order_id'] . " has been rejected. Reason: $reason");
        }
    }

    // ===== REVENUE =====
    public function getRevenueOrders() {
        $stmt = $this->conn->prepare("
            SELECT o.order_id, o.total_amount, o.order_status, o.created_at, o.delivered_at, u.user_name, u.email,
                   (SELECT refund_amount FROM ReturnRequests WHERE order_id = o.order_id AND status = 'refunded' LIMIT 1) as refund_amount
            FROM Orders o JOIN Users u ON o.user_id = u.user_id
            WHERE o.order_status = 'delivered'
            ORDER BY o.delivered_at DESC
        ");
        $stmt->execute(); return $stmt->fetchAll();
    }

    // ===== WALLET =====
    public function addToWallet($user_id, $amount) {
        $stmt = $this->conn->prepare("UPDATE Users SET wallet_balance = wallet_balance + :amt WHERE user_id = :uid");
        return $stmt->execute(['amt' => $amount, 'uid' => $user_id]);
    }

    // ===== NOTIFICATIONS =====
    public function addNotification($user_id, $message) {
        $stmt = $this->conn->prepare("INSERT INTO Notifications (user_id, message) VALUES (:uid, :msg)");
        return $stmt->execute(['uid' => $user_id, 'msg' => $message]);
    }
    // ===== ADMIN CANCEL ORDER WITH REFUND & STOCK RESTORATION =====
    public function adminCancelOrder($order_id, $reason) {
        $stmt = $this->conn->prepare("SELECT user_id, total_amount, wallet_amount, order_status FROM Orders WHERE order_id = :id");
        $stmt->execute(['id' => $order_id]);
        $order = $stmt->fetch();
        if (!$order || $order['order_status'] === 'cancelled') return false;

        try {
            $this->conn->beginTransaction();

            // Set order status to cancelled
            $this->conn->prepare("UPDATE Orders SET order_status = 'cancelled' WHERE order_id = :id")
                       ->execute(['id' => $order_id]);

            // If there's wallet amount spent, refund it!
            $refundedMsg = "";
            $walletAmount = (float)($order['wallet_amount'] ?? 0);
            if ($walletAmount > 0) {
                $this->conn->prepare("UPDATE Users SET wallet_balance = wallet_balance + :amt WHERE user_id = :uid")
                           ->execute(['amt' => $walletAmount, 'uid' => $order['user_id']]);
                $refundedMsg = " Rs " . number_format($walletAmount, 2) . " has been refunded back to your wallet.";
            }

            // Restore items stock
            $stmtItems = $this->conn->prepare("SELECT variant_id, quantity FROM OrderItems WHERE order_id = :id");
            $stmtItems->execute(['id' => $order_id]);
            foreach ($stmtItems->fetchAll() as $item) {
                if ($item['variant_id']) {
                    $this->conn->prepare("UPDATE ProductVariants SET stock = stock + :qty WHERE variant_id = :vid")
                               ->execute(['qty' => $item['quantity'], 'vid' => $item['variant_id']]);
                }
            }

            // Notify user
            $msg = "❌ Your order #$order_id has been cancelled by Admin. Reason: $reason.{$refundedMsg}";
            $this->addNotification($order['user_id'], $msg);

            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>
