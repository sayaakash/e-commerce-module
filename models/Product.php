<?php
require_once __DIR__ . '/../config/Database.php';

class Product {
    private $conn;
    private static $cache = [];

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getAllProducts($limit = 10, $offset = 0) {
        $cacheKey = "products_all_{$limit}_{$offset}";
        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        self::$cache[$cacheKey] = $products;
        return $products;
    }

    public function getProductById($id) {
        $cacheKey = "product_{$id}";
        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            self::$cache[$cacheKey] = $product;
        }
        return $product;
    }

    public function clearCache() {
        self::$cache = [];
    }

    public function createProduct($name, $description, $price, $image_path = null) {
        $stmt = $this->conn->prepare("INSERT INTO products (name, description, price, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $price);
        $stmt->bindParam(4, $image_path);
        $stmt->execute();
        $this->clearCache();
        return $this->conn->lastInsertId();
    }

    public function updateProduct($id, $name, $description, $price, $image_path = null) {
        $stmt = $this->conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image_path = ? WHERE id = ?");
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $price);
        $stmt->bindParam(4, $image_path);
        $stmt->bindParam(5, $id, PDO::PARAM_INT);
        $stmt->execute();
        $this->clearCache();
    }

    public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        $this->clearCache();
    }
}
