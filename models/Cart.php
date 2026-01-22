<?php
require_once __DIR__ . '/../config/Database.php';

class Cart {
    private $conn;
    private $sessionId;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->sessionId = session_id();
    }

    public function addItem($productId, $quantity = 1) {
        // Check if item already exists
        $stmt = $this->conn->prepare("SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ?");
        $stmt->bindParam(1, $this->sessionId);
        $stmt->bindParam(2, $productId, PDO::PARAM_INT);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update quantity
            $newQuantity = $existing['quantity'] + $quantity;
            $stmt = $this->conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $stmt->bindParam(1, $newQuantity, PDO::PARAM_INT);
            $stmt->bindParam(2, $existing['id'], PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Insert new item
            $stmt = $this->conn->prepare("INSERT INTO cart_items (session_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bindParam(1, $this->sessionId);
            $stmt->bindParam(2, $productId, PDO::PARAM_INT);
            $stmt->bindParam(3, $quantity, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public function updateItem($productId, $quantity) {
        if ($quantity <= 0) {
            $this->removeItem($productId);
            return;
        }

        $stmt = $this->conn->prepare("UPDATE cart_items SET quantity = ? WHERE session_id = ? AND product_id = ?");
        $stmt->bindParam(1, $quantity, PDO::PARAM_INT);
        $stmt->bindParam(2, $this->sessionId);
        $stmt->bindParam(3, $productId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function removeItem($productId) {
        $stmt = $this->conn->prepare("DELETE FROM cart_items WHERE session_id = ? AND product_id = ?");
        $stmt->bindParam(1, $this->sessionId);
        $stmt->bindParam(2, $productId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getItems() {
        $stmt = $this->conn->prepare("
            SELECT ci.*, p.name, p.price, p.image_path
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.session_id = ?
        ");
        $stmt->bindParam(1, $this->sessionId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemCount() {
        $stmt = $this->conn->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE session_id = ?");
        $stmt->bindParam(1, $this->sessionId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getTotal() {
        $stmt = $this->conn->prepare("
            SELECT SUM(ci.quantity * p.price) as total
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.session_id = ?
        ");
        $stmt->bindParam(1, $this->sessionId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0.00;
    }

    public function clearCart() {
        $stmt = $this->conn->prepare("DELETE FROM cart_items WHERE session_id = ?");
        $stmt->bindParam(1, $this->sessionId);
        $stmt->execute();
    }
}
