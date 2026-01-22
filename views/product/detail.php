<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Details</title>
    <link rel="stylesheet" href="/e-commerce-module/public/css/style.css">
</head>
<body>
    <header>
        <h1>E-Commerce Store Module</h1>
        <nav>
            <a href="/e-commerce-module/index.php?action=index">Products</a>
            <a href="/e-commerce-module/index.php?action=cart">Cart</a>
        </nav>
    </header>

    <main>
        <div class="product-detail-container">
            <div class="product-detail">
                <div class="product-image-section">
                    <?php if ($product['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image-large">
                    <?php else: ?>
                        <div class="no-image">
                            <span>No Image Available</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>

                    <div class="product-meta">
                        <div class="meta-item">
                            <span class="meta-label">Product ID:</span>
                            <span class="meta-value">#<?php echo htmlspecialchars($product['id']); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Category:</span>
                            <span class="meta-value">Electronics</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Availability:</span>
                            <span class="meta-value availability">In Stock</span>
                        </div>
                    </div>

                    <div class="purchase-section">
                        <form action="/e-commerce-module/index.php?action=addToCart" method="post" class="add-to-cart-form">
                            <?php echo CSRF::getTokenField(); ?>
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                            <div class="quantity-selector">
                                <label for="quantity">Quantity:</label>
                                <div class="quantity-controls">
                                    <button type="button" onclick="changeQuantity(-1)" class="quantity-btn">-</button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" readonly>
                                    <button type="button" onclick="changeQuantity(1)" class="quantity-btn">+</button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary add-to-cart-btn">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="product-actions">
                <a href="/e-commerce-module/index.php?action=index" class="btn btn-secondary">← Back to Products</a>
                <a href="/e-commerce-module/index.php?action=cart" class="btn btn-primary">View Cart</a>
            </div>
        </div>
    </main>

    <script>
        function changeQuantity(delta) {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            const newValue = Math.max(1, Math.min(99, currentValue + delta));
            input.value = newValue;
        }
    </script>

    <footer>
        <p>&copy; 2024 E-Commerce Store Module</p>
    </footer>
</body>
</html>
