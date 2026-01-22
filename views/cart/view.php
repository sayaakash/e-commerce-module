<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="/e-commerce-module/public/css/style.css">
</head>
<body>
    <header>
        <h1>E-Commerce Store Module</h1>
        <nav>
            <a href="/e-commerce-module/index.php?action=index">Products</a>
            <a href="/e-commerce-module/index.php?action=cart">Cart (<?php echo $itemCount; ?>)</a>
        </nav>
    </header>

    <main>
        <h2>Shopping Cart</h2>

        <?php if (empty($cartItems)): ?>
            <p>Your cart is empty. <a href="/e-commerce-module/index.php?action=index">Continue shopping</a></p>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <?php if ($item['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                        <?php endif; ?>

                        <div class="cart-item-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="price">$<?php echo number_format($item['price'], 2); ?> each</p>
                        </div>

                        <div class="cart-item-quantity">
                            <form action="/e-commerce-module/index.php?action=updateCart" method="post" class="quantity-form">
                                <?php echo CSRF::getTokenField(); ?>
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0" max="99">
                                <button type="submit" class="btn btn-small">Update</button>
                            </form>
                        </div>

                        <div class="cart-item-total">
                            <p>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></p>
                        </div>

                        <div class="cart-item-actions">
                            <form action="/e-commerce-module/index.php?action=removeFromCart" method="post" style="display: inline;">
                                <?php echo CSRF::getTokenField(); ?>
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-small">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Cart Summary</h3>
                <p>Total Items: <?php echo $itemCount; ?></p>
                <p>Total Price: $<?php echo number_format($total, 2); ?></p>

                <div class="cart-actions">
                    <a href="/e-commerce-module/index.php?action=index" class="btn btn-secondary">Continue Shopping</a>
                    <a href="/e-commerce-module/index.php?action=cartSummary" class="btn btn-primary">Checkout</a>
                    <form action="/e-commerce-module/index.php?action=clearCart" method="post" style="display: inline;">
                        <?php echo CSRF::getTokenField(); ?>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 E-Commerce Store Module</p>
    </footer>
</body>
</html>
