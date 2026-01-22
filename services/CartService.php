<?php
require_once __DIR__ . '/../repositories/CartRepositoryInterface.php';
require_once __DIR__ . '/../repositories/ProductRepositoryInterface.php';

class CartService {
    private CartRepositoryInterface $cartRepository;
    private ProductRepositoryInterface $productRepository;
    private string $sessionId;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->sessionId = session_id();
    }

    public function getCartItems(): array {
        return $this->cartRepository->findBySessionId($this->sessionId);
    }

    public function addToCart(int $productId, int $quantity = 1): bool {
        if ($productId <= 0 || $quantity <= 0) {
            throw new InvalidArgumentException('Invalid product ID or quantity');
        }

        // Verify product exists
        $product = $this->productRepository->findById($productId);
        if (!$product) {
            throw new InvalidArgumentException('Product not found');
        }

        return $this->cartRepository->addItem($this->sessionId, $productId, $quantity);
    }

    public function updateCartItem(int $productId, int $quantity): bool {
        if ($productId <= 0) {
            throw new InvalidArgumentException('Invalid product ID');
        }

        if ($quantity < 0) {
            throw new InvalidArgumentException('Quantity cannot be negative');
        }

        return $this->cartRepository->updateItemQuantity($this->sessionId, $productId, $quantity);
    }

    public function removeFromCart(int $productId): bool {
        if ($productId <= 0) {
            throw new InvalidArgumentException('Invalid product ID');
        }

        return $this->cartRepository->removeItem($this->sessionId, $productId);
    }

    public function clearCart(): bool {
        return $this->cartRepository->clearCart($this->sessionId);
    }

    public function getCartSummary(): array {
        $items = $this->getCartItems();
        $total = $this->cartRepository->getTotal($this->sessionId);
        $itemCount = $this->cartRepository->getItemCount($this->sessionId);

        return [
            'items' => $items,
            'total' => $total,
            'item_count' => $itemCount
        ];
    }

    public function getItemCount(): int {
        return $this->cartRepository->getItemCount($this->sessionId);
    }

    public function getTotal(): float {
        return $this->cartRepository->getTotal($this->sessionId);
    }

    public function validateCartData(array $data): array {
        $errors = [];

        $productId = $data['product_id'] ?? null;
        if (!is_numeric($productId) || $productId <= 0) {
            $errors['product_id'] = 'Valid product ID is required';
        }

        $quantity = $data['quantity'] ?? null;
        if (!is_numeric($quantity) || $quantity <= 0) {
            $errors['quantity'] = 'Valid quantity is required';
        } elseif ($quantity > 99) { // Reasonable limit
            $errors['quantity'] = 'Quantity cannot exceed 99';
        }

        if (!empty($errors)) {
            throw new ValidationException('Cart validation failed', $errors);
        }

        return [
            'product_id' => (int)$productId,
            'quantity' => (int)$quantity
        ];
    }
}
