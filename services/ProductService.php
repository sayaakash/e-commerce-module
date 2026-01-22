<?php
require_once __DIR__ . '/../repositories/ProductRepositoryInterface.php';

class ProductService {
    private ProductRepositoryInterface $productRepository;
    private array $cache = [];

    public function __construct(ProductRepositoryInterface $productRepository) {
        $this->productRepository = $productRepository;
    }

    public function getProducts(int $limit = 10, int $offset = 0): array {
        $cacheKey = "products_{$limit}_{$offset}";

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->productRepository->findAll($limit, $offset);
        }

        return $this->cache[$cacheKey];
    }

    public function getProduct(int $id): ?array {
        if ($id <= 0) {
            return null;
        }

        $cacheKey = "product_{$id}";

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->productRepository->findById($id);
        }

        return $this->cache[$cacheKey];
    }

    public function validateProductData(array $data): array {
        $errors = [];

        $name = trim($data['name'] ?? '');
        if (empty($name)) {
            $errors['name'] = 'Product name is required';
        } elseif (strlen($name) > 255) {
            $errors['name'] = 'Product name must be less than 255 characters';
        }

        $description = trim($data['description'] ?? '');
        if (strlen($description) > 10000) { // Reasonable limit
            $errors['description'] = 'Description is too long';
        }

        $price = $data['price'] ?? null;
        if (!is_numeric($price) || $price <= 0) {
            $errors['price'] = 'Valid price is required';
        } elseif ($price > 999999.99) { // Database constraint
            $errors['price'] = 'Price is too high';
        }

        $imagePath = trim($data['image_path'] ?? '');
        if (!empty($imagePath) && !filter_var($imagePath, FILTER_VALIDATE_URL)) {
            $errors['image_path'] = 'Invalid image URL';
        }

        if (!empty($errors)) {
            throw new ValidationException('Product validation failed', $errors);
        }

        return [
            'name' => htmlspecialchars($name),
            'description' => htmlspecialchars($description),
            'price' => (float)$price,
            'image_path' => $imagePath ?: null
        ];
    }

    public function createProduct(array $data): int {
        $validatedData = $this->validateProductData($data);

        $productId = $this->productRepository->create($validatedData);

        // Clear cache
        $this->clearCache();

        return $productId;
    }

    public function updateProduct(int $id, array $data): bool {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid product ID');
        }

        $validatedData = $this->validateProductData($data);

        $result = $this->productRepository->update($id, $validatedData);

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    public function deleteProduct(int $id): bool {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid product ID');
        }

        $result = $this->productRepository->delete($id);

        if ($result) {
            $this->clearCache();
        }

        return $result;
    }

    public function getTotalProducts(): int {
        return $this->productRepository->count();
    }

    public function clearCache(): void {
        $this->cache = [];
    }
}
