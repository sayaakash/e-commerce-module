<?php
require_once __DIR__ . '/../services/ProductService.php';
require_once __DIR__ . '/../services/CartService.php';

class ProductController {
    private ProductService $productService;
    private CartService $cartService;

    public function __construct(ProductService $productService, ?CartService $cartService = null) {
        $this->productService = $productService;
        $this->cartService = $cartService;
    }

    public function index(): void {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $products = $this->productService->getProducts($limit, $offset);
            $cartItemCount = $this->cartService ? $this->cartService->getItemCount() : 0;

            include __DIR__ . '/../views/product/list.php';
        } catch (Exception $e) {
            $this->handleError($e, 'Unable to load products');
        }
    }

    public function show($id): void {
        try {
            $id = (int)$id;
            if ($id <= 0) {
                throw new InvalidArgumentException('Invalid product ID');
            }

            $product = $this->productService->getProduct($id);
            if (!$product) {
                http_response_code(404);
                echo 'Product not found';
                return;
            }

            include __DIR__ . '/../views/product/detail.php';
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo 'Invalid request';
        } catch (Exception $e) {
            $this->handleError($e, 'Unable to load product');
        }
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->productService->createProduct($_POST);
                header('Location: /e-commerce-module/index.php?action=index');
                exit;
            } catch (ValidationException $e) {
                $errors = $e->getErrors();
                include __DIR__ . '/../views/product/create.php';
                return;
            } catch (Exception $e) {
                $this->handleError($e, 'Unable to create product');
            }
        }

        include __DIR__ . '/../views/product/create.php';
    }

    private function handleError(Exception $e, string $message): void {
        error_log($e->getMessage());
        http_response_code(500);
        echo htmlspecialchars($message);
    }
}
