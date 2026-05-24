<?php
require_once 'controllers/Controller.php';

class UserController extends Controller {

    private function getDB() {
        require_once 'config/database.php';
        return (new Database())->getConnection();
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        $db = $this->getDB();
        $stmt = $db->prepare("SELECT * FROM Users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmtAddr = $db->prepare("SELECT * FROM Addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
        $stmtAddr->execute([$_SESSION['user_id']]);
        $addresses = $stmtAddr->fetchAll(PDO::FETCH_ASSOC);

        // Notifications — 4 most recent for profile preview
        $notifs = [];
        $totalNotifs = 0;
        $unreadNotifs = 0;
        try {
            $s = $db->prepare("SELECT * FROM Notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 4");
            $s->execute([$_SESSION['user_id']]);
            $notifs = $s->fetchAll(PDO::FETCH_ASSOC);
            $sc = $db->prepare("SELECT COUNT(*) FROM Notifications WHERE user_id = ?");
            $sc->execute([$_SESSION['user_id']]);
            $totalNotifs = $sc->fetchColumn();
            $su = $db->prepare("SELECT COUNT(*) FROM Notifications WHERE user_id = ? AND is_read = 0");
            $su->execute([$_SESSION['user_id']]);
            $unreadNotifs = $su->fetchColumn();
        } catch(PDOException $e) {}

        $this->view('user/profile', [
            'user' => $user,
            'addresses' => $addresses,
            'notifications' => $notifs,
            'total_notifications' => $totalNotifs,
            'unread_notifications' => $unreadNotifs
        ]);
    }

    public function notifications() {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        $db = $this->getDB();
        $stmt = $db->prepare("SELECT * FROM Notifications WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Mark ALL as read so the bell badge clears
        $db->prepare("UPDATE Notifications SET is_read = 1 WHERE user_id = ?")->execute([$_SESSION['user_id']]);
        $this->view('user/notifications', ['notifications' => $notifs]);
    }

    public function addAddress() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $db = $this->getDB();
            $db->prepare("INSERT INTO Addresses (user_id,full_name,phone,address_line,city,province,zipcode) VALUES (?,?,?,?,?,?,?)")
               ->execute([$_SESSION['user_id'],$_POST['full_name'],$_POST['phone'],$_POST['address_line'],$_POST['city'],$_POST['province'],substr($_POST['zipcode']??'',0,5)]);
            header("Location: /user/profile"); exit;
        }
    }

    public function deleteAddress($id) {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        $db = $this->getDB();
        $c = $db->prepare("SELECT COUNT(*) FROM Addresses WHERE user_id = ?");
        $c->execute([$_SESSION['user_id']]);
        if ($c->fetchColumn() > 1) {
            $db->prepare("DELETE FROM Addresses WHERE address_id = ? AND user_id = ?")->execute([$id, $_SESSION['user_id']]);
        }
        header("Location: /user/profile"); exit;
    }

    public function purchases() {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        $db = $this->getDB();
        $userId = $_SESSION['user_id'];

        // Auto-deliver 3-day shipped orders for this user
        try {
            $db->prepare("UPDATE Orders SET order_status='delivered', delivered_at=NOW() WHERE user_id=? AND order_status='shipped' AND TIMESTAMPDIFF(HOUR, COALESCE(dispatched_at,created_at), NOW()) >= 72")->execute([$userId]);
        } catch(Exception $e) {}

        $stmtOrders = $db->prepare("SELECT o.* FROM Orders o WHERE o.user_id = ? ORDER BY o.created_at DESC");
        $stmtOrders->execute([$userId]);
        $ordersList = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

        $orders = [];
        foreach ($ordersList as $order) {
            $si = $db->prepare("SELECT oi.*, p.name as product_name, p.product_id, pi.image_url, pv.variant_key, pv.price FROM OrderItems oi JOIN ProductVariants pv ON oi.variant_id = pv.variant_id JOIN Products p ON pv.product_id = p.product_id LEFT JOIN ProductImages pi ON p.product_id = pi.product_id AND pi.is_default = 1 WHERE oi.order_id = ?");
            $si->execute([$order['order_id']]);
            $order['items'] = $si->fetchAll(PDO::FETCH_ASSOC);

            $sr = $db->prepare("SELECT * FROM ReturnRequests WHERE order_id = ? AND user_id = ?");
            $sr->execute([$order['order_id'], $userId]);
            $order['return_request'] = $sr->fetch(PDO::FETCH_ASSOC);

            $orders[] = $order;
        }

        $this->view('user/purchases', ['orders' => $orders]);
    }

    public function cancelOrder($id) {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        $db = $this->getDB();
        $stmt = $db->prepare("SELECT * FROM Orders WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($order && $order['order_status'] === 'pending' && (time() - strtotime($order['created_at'])) < 3600) {
            $db->prepare("UPDATE Orders SET order_status='cancelled' WHERE order_id = ?")->execute([$id]);
        }
        header("Location: /user/purchases"); exit;
    }

    public function confirm_received($order_id) {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        $db = $this->getDB();
        $stmt = $db->prepare("SELECT * FROM Orders WHERE order_id = ? AND user_id = ? AND order_status = 'shipped'");
        $stmt->execute([$order_id, $_SESSION['user_id']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($order) {
            $dispatchedAt = !empty($order['dispatched_at']) ? strtotime($order['dispatched_at']) : strtotime($order['created_at']);
            // Unlock after exactly 48 hours (172800 seconds = 2 days) from dispatch
            if ((time() - $dispatchedAt) >= 172800) {
                $db->prepare("UPDATE Orders SET order_status='delivered', delivered_at=NOW() WHERE order_id=?")->execute([$order_id]);
            }
        }
        header("Location: /user/purchases"); exit;
    }

    public function request_return($order_id) {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = $this->getDB();
            // Only allow if order is delivered and within 2 days (48hrs) of delivery
            $stmt = $db->prepare("SELECT * FROM Orders WHERE order_id = ? AND user_id = ? AND order_status = 'delivered'");
            $stmt->execute([$order_id, $_SESSION['user_id']]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($order) {
                $deliveredAt = !empty($order['delivered_at']) ? strtotime($order['delivered_at']) : time();
                if ((time() - $deliveredAt) < 48 * 3600) {
                    // Check no existing request
                    $sc = $db->prepare("SELECT COUNT(*) FROM ReturnRequests WHERE order_id = ? AND user_id = ?");
                    $sc->execute([$order_id, $_SESSION['user_id']]);
                    if ($sc->fetchColumn() == 0) {
                        $img_url = null;
                        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                            $dir = "assets/img/returns/"; if(!is_dir($dir)) mkdir($dir,0777,true);
                            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                            $f = $dir.'return_'.$order_id.'_'.time().'.'.$ext;
                            if(move_uploaded_file($_FILES['image']['tmp_name'], $f)) $img_url = '/'.$f;
                        }
                        $db->prepare("INSERT INTO ReturnRequests (order_id,user_id,reason,image_url) VALUES (?,?,?,?)")
                           ->execute([$order_id, $_SESSION['user_id'], $_POST['reason'], $img_url]);
                        // Notify admin via notification to user (for now record it)
                        $db->prepare("INSERT INTO Notifications (user_id, message) VALUES (?, ?)")
                           ->execute([$_SESSION['user_id'], "🔄 Your return request for Order #$order_id has been submitted and is under review."]);
                    }
                }
            }
        }
        header("Location: /user/purchases"); exit;
    }

    public function mark_return_sent($return_id) {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        $db = $this->getDB();
        // Verify return belongs to this user and is approved (r.* already includes order_id)
        $stmt = $db->prepare("SELECT r.* FROM ReturnRequests r WHERE r.return_id = ? AND r.user_id = ? AND r.status = 'approved'");
        $stmt->execute([$return_id, $_SESSION['user_id']]);
        $ret = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($ret) {
            $db->prepare("UPDATE ReturnRequests SET status='returning' WHERE return_id=?")->execute([$return_id]);
            $db->prepare("INSERT INTO Notifications (user_id, message) VALUES (?, ?)")
               ->execute([$_SESSION['user_id'], "📦 We've noted that you sent back your product for Order #".$ret['order_id'].". Admin will inspect and process your refund."]);
        }
        header("Location: /user/returns"); exit;
    }

    public function returns() {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        $db = $this->getDB();
        $stmt = $db->prepare("SELECT r.*, o.total_amount, o.delivered_at FROM ReturnRequests r JOIN Orders o ON r.order_id = o.order_id WHERE r.user_id = ? ORDER BY r.created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $returns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view('user/returns', ['returns' => $returns]);
    }

    public function messages() {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        $this->view('user/messages', []);
    }
    public function send_message() {
        if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
            $db = $this->getDB();
            $db->prepare("INSERT INTO Messages (sender_role,user_id,message) VALUES ('customer',:uid,:msg)")
               ->execute(['uid' => $_SESSION['user_id'], 'msg' => trim($_POST['message'])]);
        }
        header("Location: /user/messages"); exit;
    }
}
?>
