<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Summary - Checkout</title>
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
        <h2>Order Summary</h2>

        <?php if (empty($cartItems)): ?>
            <p>Your cart is empty. <a href="/e-commerce-module/index.php?action=index">Continue shopping</a></p>
        <?php else: ?>
            <div class="order-summary">
                <h3>Items in your cart:</h3>
                <div class="summary-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="summary-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                            <span>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-total">
                    <strong>Total Items: <?php echo $itemCount; ?></strong><br>
                    <strong>Total Amount: $<?php echo number_format($total, 2); ?></strong>
                </div>

                <div class="checkout-notice">
                    <p><em>Note: This is a demo e-commerce module. In a real application, you would proceed to payment processing here.</em></p>
                </div>

                <div class="checkout-actions">
                    <a href="/e-commerce-module/index.php?action=cart" class="btn btn-secondary">Back to Cart</a>
                    <button onclick="alert('Thank you for your order! This is a demo.')" class="btn btn-primary">Complete Order</button>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 E-Commerce Store Module</p>
    </footer>
</body>
</html>
