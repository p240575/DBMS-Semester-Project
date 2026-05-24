<?php
require_once 'controllers/Controller.php';

class CategoryController extends Controller {
    public function show($id) {
        $productModel = $this->model('Product');
        $products = $productModel->getByCategory($id);
        
        require_once 'config/database.php';
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM Categories WHERE category_id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();

        $this->view('categories/show', ['products' => $products, 'category' => $category]);
    }
}
?>
