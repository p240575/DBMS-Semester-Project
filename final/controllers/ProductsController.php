<?php
require_once 'controllers/Controller.php';

class ProductsController extends Controller {
    private function getCategories() {
        require_once 'config/database.php';
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM Categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function index() {
        $productModel = $this->model('Product');
        $categories = $this->getCategories();

        $category_id = isset($_GET['category']) ? $_GET['category'] : null;

        if ($category_id) {
            $products = $productModel->getByCategory($category_id);
        } else {
            $products = $productModel->getAll();
        }

        $this->view('products/index', [
            'products' => $products, 
            'categories' => $categories, 
            'current_category' => $category_id
        ]);
    }

    public function show($id) {
        $productModel = $this->model('Product');
        $product = $productModel->getById($id);
        
        require_once 'config/database.php';
        $db = (new Database())->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM Reviews WHERE product_id = ? ORDER BY created_at DESC");
        $stmt->execute([$id]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtAvg = $db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM Reviews WHERE product_id = ?");
        $stmtAvg->execute([$id]);
        $ratingData = $stmtAvg->fetch(PDO::FETCH_ASSOC);

        $stmtCat = $db->prepare("SELECT category_id FROM ProductCategories WHERE product_id = ? LIMIT 1");
        $stmtCat->execute([$id]);
        $cat = $stmtCat->fetch(PDO::FETCH_ASSOC);
        $has_size = ($cat && in_array($cat['category_id'], [2, 3]));

        $data = [
            'title' => $product['name'] ?? 'Product', 
            'product' => $product, 
            'reviews' => $reviews,
            'rating' => $ratingData,
            'has_size' => $has_size
        ];
        $this->view('products/show', $data);
    }

    public function addReview() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
            require_once 'config/database.php';
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("INSERT INTO Reviews (product_id, user_name, user_image, rating, comment) VALUES (?, ?, ?, ?, ?)");
            $name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest User';
            $rating = $_POST['rating'] ?? 5;
            $stmt->execute([$_POST['product_id'], $name, null, $rating, $_POST['comment']]);
            header("Location: /products/show/" . $_POST['product_id']);
            exit;
        }
    }

    public function search() {
        $q = $_GET['q'] ?? '';
        $productModel = $this->model('Product');
        $products = $productModel->search($q);
        $categories = $this->getCategories();
        $this->view('products/index', ['products' => $products, 'categories' => $categories, 'current_category' => null, 'search_query' => $q]);
    }

    public function bestselling() {
        $productModel = $this->model('Product');
        $products = $productModel->getAll();
        shuffle($products);
        $products = array_slice($products, 0, 30);
        $categories = $this->getCategories();
        $this->view('products/index', ['products' => $products, 'categories' => $categories, 'current_category' => null, 'title' => 'Best Selling Products']);
    }

    public function discounted() {
        $productModel = $this->model('Product');
        $products = $productModel->getAll();
        shuffle($products);
        $products = array_slice($products, 0, 30);
        $categories = $this->getCategories();
        $this->view('products/index', ['products' => $products, 'categories' => $categories, 'current_category' => null, 'title' => 'Discounted Products']);
    }

    public function trending() {
        $productModel = $this->model('Product');
        $products = $productModel->getAll();
        shuffle($products);
        $products = array_slice($products, 0, 6);
        $categories = $this->getCategories();
        $this->view('products/index', ['products' => $products, 'categories' => $categories, 'current_category' => null, 'title' => 'Trending Now']);
    }
}
?>
