<?php
require_once __DIR__ . '/../services/CartService.php';

class CartController {
    private CartService $cartService;

    public function __construct(CartService $cartService) {
        $this->cartService = $cartService;
    }

    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $validatedData = $this->cartService->validateCartData($_POST);
                $this->cartService->addToCart($validatedData['product_id'], $validatedData['quantity']);
                header('Location: /e-commerce-module/index.php?action=cart');
                exit;
            } catch (ValidationException $e) {
                Session::set('errors', $e->getErrors());
                header('Location: /e-commerce-module/index.php?action=index');
                exit;
            } catch (Exception $e) {
                $this->handleError($e, 'Unable to add item to cart');
            }
        }

        header('Location: /e-commerce-module/index.php?action=index');
        exit;
    }

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $productId = (int)($_POST['product_id'] ?? 0);
                $quantity = (int)($_POST['quantity'] ?? 0);

                if ($productId <= 0) {
                    throw new InvalidArgumentException('Invalid product ID');
                }

                $this->cartService->updateCartItem($productId, $quantity);
                header('Location: /e-commerce-module/index.php?action=cart');
                exit;
            } catch (Exception $e) {
                $this->handleError($e, 'Unable to update cart item');
            }
        }

        header('Location: /e-commerce-module/index.php?action=cart');
        exit;
    }

    public function remove(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $productId = (int)($_POST['product_id'] ?? 0);

                if ($productId <= 0) {
                    throw new InvalidArgumentException('Invalid product ID');
                }

                $this->cartService->removeFromCart($productId);
                header('Location: /e-commerce-module/index.php?action=cart');
                exit;
            } catch (Exception $e) {
                $this->handleError($e, 'Unable to remove item from cart');
            }
        }

        header('Location: /e-commerce-module/index.php?action=cart');
        exit;
    }

    public function view(): void {
        try {
            $cartSummary = $this->cartService->getCartSummary();
            $cartItems = $cartSummary['items'];
            $total = $cartSummary['total'];
            $itemCount = $cartSummary['item_count'];

            include __DIR__ . '/../views/cart/view.php';
        } catch (Exception $e) {
            $this->handleError($e, 'Unable to load cart');
        }
    }

    public function summary(): void {
        try {
            $cartSummary = $this->cartService->getCartSummary();
            $cartItems = $cartSummary['items'];
            $total = $cartSummary['total'];
            $itemCount = $cartSummary['item_count'];

            include __DIR__ . '/../views/cart/summary.php';
        } catch (Exception $e) {
            $this->handleError($e, 'Unable to load cart summary');
        }
    }

    public function clear(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->cartService->clearCart();
                header('Location: /e-commerce-module/index.php?action=cart');
                exit;
            } catch (Exception $e) {
                $this->handleError($e, 'Unable to clear cart');
            }
        }

        header('Location: /e-commerce-module/index.php?action=cart');
        exit;
    }

    private function handleError(Exception $e, string $message): void {
        error_log($e->getMessage());
        http_response_code(500);
        echo htmlspecialchars($message);
    }
}
