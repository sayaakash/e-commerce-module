<?php
// Bootstrap the application
require_once 'bootstrap/app.php';

// Start secure session
Session::start();

// Validate CSRF for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();
}

// Get routing parameters
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Route to appropriate controller
switch ($action) {
    case 'index':
        $controller = $container->get(ProductController::class);
        $controller->index();
        break;

    case 'show':
        $controller = $container->get(ProductController::class);
        $controller->show($id);
        break;

    case 'addToCart':
        $controller = $container->get(CartController::class);
        $controller->add();
        break;

    case 'cart':
        $controller = $container->get(CartController::class);
        $controller->view();
        break;

    case 'updateCart':
        $controller = $container->get(CartController::class);
        $controller->update();
        break;

    case 'removeFromCart':
        $controller = $container->get(CartController::class);
        $controller->remove();
        break;

    case 'cartSummary':
        $controller = $container->get(CartController::class);
        $controller->summary();
        break;

    case 'clearCart':
        $controller = $container->get(CartController::class);
        $controller->clear();
        break;

    default:
        // Default to product list
        $controller = $container->get(ProductController::class);
        $controller->index();
        break;
}
