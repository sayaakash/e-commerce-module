<?php
// Autoload all classes
spl_autoload_register(function ($className) {
    $paths = [
        'controllers/',
        'models/',
        'config/',
        'repositories/',
        'services/',
        'exceptions/'
    ];

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Create dependency injection container
$container = new Container();

// Register database connection
$container->set(PDO::class, function() {
    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=ecommerce_db;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
});

// Register repositories
$container->set(ProductRepositoryInterface::class, function($c) {
    return new ProductRepository($c->get(PDO::class));
});

$container->set(CartRepositoryInterface::class, function($c) {
    return new CartRepository($c->get(PDO::class));
});

// Register services
$container->set(ProductService::class, function($c) {
    return new ProductService($c->get(ProductRepositoryInterface::class));
});

$container->set(CartService::class, function($c) {
    return new CartService(
        $c->get(CartRepositoryInterface::class),
        $c->get(ProductRepositoryInterface::class)
    );
});

// Register controllers
$container->set(ProductController::class, function($c) {
    return new ProductController(
        $c->get(ProductService::class),
        $c->get(CartService::class)
    );
});

$container->set(CartController::class, function($c) {
    return new CartController($c->get(CartService::class));
});
