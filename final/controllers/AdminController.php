<?php
require_once 'controllers/Controller.php';

class AdminController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: /auth/login"); exit;
        }
    }

    public function index() {
        $adminModel = $this->model('Admin');
        $adminModel->autoDeliverOldOrders();
        $stats = $adminModel->getDashboardStats();
        $this->view('admin/index', ['stats' => $stats]);
    }

    public function products() {
        $adminModel = $this->model('Admin');
        $this->view('admin/products', ['products' => $adminModel->getAllProducts(), 'categories' => $adminModel->getAllCategories()]);
    }

    public function add_product() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $adminModel = $this->model('Admin');
            $img = '/assets/img/hero_1.png';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $dir = "assets/img/"; if(!is_dir($dir)) mkdir($dir,0777,true);
                $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $f = $dir.'product_'.time().'.'.$ext;
                if(move_uploaded_file($_FILES["image"]["tmp_name"],$f)) $img = '/'.$f;
            }
            $vk_arr = $_POST['variant_keys'] ?? ['Default'];
            $price_arr = $_POST['variant_prices'] ?? [$_POST['price'] ?? 0];
            $stock_arr = $_POST['variant_stocks'] ?? [$_POST['stock'] ?? 0];

            $adminModel->addProduct($_POST['name'], $_POST['description']??'', $_POST['category_id'], $img, $vk_arr, $price_arr, $stock_arr);
            header("Location: /admin/products"); exit;
        }
    }

    public function update_product() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $adminModel = $this->model('Admin');
            $img = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $dir = "assets/img/"; if(!is_dir($dir)) mkdir($dir,0777,true);
                $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $f = $dir.'product_'.time().'.'.$ext;
                if(move_uploaded_file($_FILES["image"]["tmp_name"],$f)) $img = '/'.$f;
            }
            $nvk_arr = $_POST['new_variant_keys'] ?? [];
            $nvp_arr = $_POST['new_variant_prices'] ?? [];
            $nvs_arr = $_POST['new_variant_stocks'] ?? [];

            $adminModel->updateProduct(
                $_POST['product_id'],
                $_POST['name'],
                $_POST['description']??'',
                $_POST['price'],
                $_POST['stock'],
                $_POST['variant_id'],
                $_POST['variant_key']??'Default',
                $img,
                $nvk_arr,
                $nvp_arr,
                $nvs_arr
            );
            header("Location: /admin/products"); exit;
        }
    }

    public function delete_product($id) {
        if ($id) $this->model('Admin')->deleteProduct($id);
        header("Location: /admin/products"); exit;
    }

    public function orders() {
        $adminModel = $this->model('Admin');
        $adminModel->autoDeliverOldOrders();
        $this->view('admin/orders', ['orders' => $adminModel->getOrders()]);
    }

    public function dispatch_order($id) {
        if ($id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model('Admin')->dispatchOrderWithAgent($id, $_POST['delivery_agent'] ?? 'Unknown');
        }
        header("Location: /admin/orders"); exit;
    }

    public function cancel_order($id) {
        if ($id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $reason = $_POST['reason'] ?? 'Order cancelled by administrator.';
            $this->model('Admin')->adminCancelOrder($id, $reason);
        }
        header("Location: /admin/orders"); exit;
    }

    public function customers() {
        $this->view('admin/customers', ['customers' => $this->model('Admin')->getCustomers()]);
    }

    public function coupons() {
        $this->view('admin/coupons', ['coupons' => $this->model('Admin')->getCoupons()]);
    }
    public function add_coupon() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model('Admin')->addCoupon($_POST['code'],$_POST['discount_percent']);
            header("Location: /admin/coupons"); exit;
        }
    }
    public function delete_coupon($id) {
        if ($id) $this->model('Admin')->deleteCoupon($id);
        header("Location: /admin/coupons"); exit;
    }

    public function delete_customer($id) {
        if ($id) $this->model('Admin')->deleteCustomer($id);
        header("Location: /admin/customers"); exit;
    }
    public function send_notice($id) {
        if ($id && $_SERVER['REQUEST_METHOD'] == 'POST') $this->model('Admin')->sendNotice($id, $_POST['message']);
        header("Location: /admin/customers"); exit;
    }

    public function reviews() {
        $this->view('admin/reviews', ['reviews' => $this->model('Admin')->getReviews()]);
    }
    public function delete_review($id) {
        if ($id) $this->model('Admin')->deleteReview($id);
        header("Location: /admin/reviews"); exit;
    }
    public function reply_review($id) {
        if ($id && $_SERVER['REQUEST_METHOD'] == 'POST') $this->model('Admin')->replyReview($id, $_POST['reply']);
        header("Location: /admin/reviews"); exit;
    }

    public function messages() {
        $this->view('admin/messages', []);
    }
    public function send_message($user_id) {
        if ($user_id && $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
            require_once 'config/database.php';
            $db = (new Database())->getConnection();
            $db->prepare("INSERT INTO Messages (sender_role, user_id, message) VALUES ('admin', :uid, :msg)")
               ->execute(['uid' => $user_id, 'msg' => trim($_POST['message'])]);
        }
        header("Location: /admin/messages?user_id=$user_id"); exit;
    }

    // ===== REVENUE =====
    public function revenue() {
        $adminModel = $this->model('Admin');
        $this->view('admin/revenue', ['orders' => $adminModel->getRevenueOrders()]);
    }

    // ===== RETURNS =====
    public function returns() {
        $adminModel = $this->model('Admin');
        $adminModel->autoDeliverOldOrders();
        $this->view('admin/returns', ['returns' => $adminModel->getReturnRequests()]);
    }

    public function approve_return($id) {
        if ($id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model('Admin')->approveReturn($id, $_POST['return_address'], $_POST['admin_note'] ?? '');
        }
        header("Location: /admin/returns"); exit;
    }

    public function received_return($id) {
        if ($id) $this->model('Admin')->markReturnReceived($id);
        header("Location: /admin/returns"); exit;
    }

    public function process_refund($id) {
        if ($id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model('Admin')->processRefund($id, $_POST['refund_amount'], $_POST['admin_note'] ?? '');
        }
        header("Location: /admin/returns"); exit;
    }

    public function reject_return($id) {
        if ($id && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->model('Admin')->rejectReturn($id, $_POST['admin_note'] ?? '');
        }
        header("Location: /admin/returns"); exit;
    }
}
?>
