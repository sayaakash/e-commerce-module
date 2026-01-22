<?php
interface CartRepositoryInterface {
    public function findBySessionId(string $sessionId): array;
    public function addItem(string $sessionId, int $productId, int $quantity): bool;
    public function updateItemQuantity(string $sessionId, int $productId, int $quantity): bool;
    public function removeItem(string $sessionId, int $productId): bool;
    public function clearCart(string $sessionId): bool;
    public function getItemCount(string $sessionId): int;
    public function getTotal(string $sessionId): float;
}
