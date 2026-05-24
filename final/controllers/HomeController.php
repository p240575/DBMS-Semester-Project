<?php
require_once 'controllers/Controller.php';

class HomeController extends Controller {
    public function index() {
        require_once 'models/Product.php';
        $productModel = new Product();
        $products = $productModel->getAll();
        shuffle($products);
        $bestselling = array_slice($products, 0, 8);
        $discounted = array_slice($products, 8, 8);
        
        $data = ['title' => 'Home', 'bestselling' => $bestselling, 'discounted' => $discounted];
        $this->view('home', $data);
    }

    public function about() {
        $this->view('home/about');
    }

    public function refund() {
        $this->view('home/refund');
    }

    public function help() {
        $this->view('home/help');
    }

    public function reviews() {
        require_once 'config/database.php';
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT r.*, p.name as product_name FROM Reviews r JOIN Products p ON r.product_id = p.product_id ORDER BY r.created_at DESC LIMIT 40");
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view('home/reviews', ['reviews' => $reviews]);
    }
}
?>
