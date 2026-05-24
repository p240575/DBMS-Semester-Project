<?php
class Product {
    private $conn;

    public function __construct() {
        require_once 'config/database.php';
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $query = "SELECT p.*, pv.price, pi.image_url 
                  FROM Products p 
                  LEFT JOIN ProductVariants pv ON p.product_id = pv.product_id 
                  LEFT JOIN ProductImages pi ON p.product_id = pi.product_id AND pi.is_default = 1
                  WHERE p.status = 'active'
                  GROUP BY p.product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT p.*, pv.price, pi.image_url, pv.stock
                  FROM Products p 
                  LEFT JOIN ProductVariants pv ON p.product_id = pv.product_id 
                  LEFT JOIN ProductImages pi ON p.product_id = pi.product_id AND pi.is_default = 1
                  WHERE p.product_id = :id
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getByCategory($category_id) {
        $query = "SELECT p.*, pv.price, pi.image_url 
                  FROM Products p 
                  JOIN ProductCategories pc ON p.product_id = pc.product_id
                  LEFT JOIN ProductVariants pv ON p.product_id = pv.product_id 
                  LEFT JOIN ProductImages pi ON p.product_id = pi.product_id AND pi.is_default = 1
                  WHERE p.status = 'active' AND pc.category_id = :cid
                  GROUP BY p.product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['cid' => $category_id]);
        return $stmt->fetchAll();
    }

    public function search($q) {
        $q = str_replace(' ', '%', $q);
        $query = "SELECT p.*, pv.price, pi.image_url 
                  FROM Products p 
                  LEFT JOIN ProductVariants pv ON p.product_id = pv.product_id 
                  LEFT JOIN ProductImages pi ON p.product_id = pi.product_id AND pi.is_default = 1
                  WHERE p.status = 'active' AND (p.name LIKE :q OR p.description LIKE :q)
                  GROUP BY p.product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['q' => "%$q%"]);
        return $stmt->fetchAll();
    }
}
?>
