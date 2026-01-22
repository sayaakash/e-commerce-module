<?php
require_once __DIR__ . '/../config/Database.php';

class ProductRepository implements ProductRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAll(int $limit = 10, int $offset = 0): array {
        $stmt = $this->pdo->prepare("
            SELECT id, name, description, price, image_path, created_at
            FROM products
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT id, name, description, price, image_path, created_at
            FROM products
            WHERE id = :id
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, description, price, image_path, created_at)
            VALUES (:name, :description, :price, :image_path, NOW())
        ");
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':image_path', $data['image_path'] ?? null);
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $stmt = $this->pdo->prepare("
            UPDATE products
            SET " . implode(', ', $fields) . "
            WHERE id = :id
        ");
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function count(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }
}
