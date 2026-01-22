<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link rel="stylesheet" href="/e-commerce-module/public/css/style.css">
</head>
<body>
    <header>
        <h1>E-Commerce Store Module</h1>
        <nav>
            <a href="/e-commerce-module/index.php?action=index">Products</a>
            <a href="/e-commerce-module/index.php?action=cart">Cart (<?php echo $cartItemCount; ?>)</a>
        </nav>
    </header>

    <main>
        <h2>Product List</h2>

        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if ($product['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                    <?php endif; ?>

                    <div class="product-content">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                    </div>

                    <div class="product-actions">
                        <a href="/e-commerce-module/index.php?action=show&id=<?php echo $product['id']; ?>" class="btn btn-secondary">View Details</a>
                        <form action="/e-commerce-module/index.php?action=addToCart" method="post" style="display: inline;">
                            <?php echo CSRF::getTokenField(); ?>
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary">Add to Cart</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products)): ?>
            <p>No products found.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 E-Commerce Store Module</p>
    </footer>
</body>
</html>
