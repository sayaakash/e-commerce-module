<?php
require_once __DIR__ . '/../config/Database.php';

class CartRepository implements CartRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findBySessionId(string $sessionId): array {
        $stmt = $this->pdo->prepare("
            SELECT ci.*, p.name, p.price, p.image_path
            FROM cart_items ci
            INNER JOIN products p ON ci.product_id = p.id
            WHERE ci.session_id = :session_id
            ORDER BY ci.created_at
        ");
        $stmt->bindValue(':session_id', $sessionId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addItem(string $sessionId, int $productId, int $quantity): bool {
        // Check if item already exists
        $stmt = $this->pdo->prepare("
            SELECT id, quantity FROM cart_items
            WHERE session_id = :session_id AND product_id = :product_id
        ");
        $stmt->bindValue(':session_id', $sessionId);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update quantity
            $newQuantity = $existing['quantity'] + $quantity;
            $stmt = $this->pdo->prepare("
                UPDATE cart_items SET quantity = :quantity, updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
            $stmt->bindValue(':id', $existing['id'], PDO::PARAM_INT);
            return $stmt->execute();
        } else {
            // Insert new item
            $stmt = $this->pdo->prepare("
                INSERT INTO cart_items (session_id, product_id, quantity, created_at, updated_at)
                VALUES (:session_id, :product_id, :quantity, NOW(), NOW())
            ");
            $stmt->bindValue(':session_id', $sessionId);
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }

    public function updateItemQuantity(string $sessionId, int $productId, int $quantity): bool {
        if ($quantity <= 0) {
            return $this->removeItem($sessionId, $productId);
        }

        $stmt = $this->pdo->prepare("
            UPDATE cart_items SET quantity = :quantity, updated_at = NOW()
            WHERE session_id = :session_id AND product_id = :product_id
        ");
        $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindValue(':session_id', $sessionId);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function removeItem(string $sessionId, int $productId): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM cart_items
            WHERE session_id = :session_id AND product_id = :product_id
        ");
        $stmt->bindValue(':session_id', $sessionId);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function clearCart(string $sessionId): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM cart_items WHERE session_id = :session_id
        ");
        $stmt->bindValue(':session_id', $sessionId);
        return $stmt->execute();
    }

    public function getItemCount(string $sessionId): int {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(quantity), 0) as total
            FROM cart_items
            WHERE session_id = :session_id
        ");
        $stmt->bindValue(':session_id', $sessionId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function getTotal(string $sessionId): float {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(ci.quantity * p.price), 0.00) as total
            FROM cart_items ci
            INNER JOIN products p ON ci.product_id = p.id
            WHERE ci.session_id = :session_id
        ");
        $stmt->bindValue(':session_id', $sessionId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$result['total'];
    }
}
