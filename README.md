# E-Commerce Store Module

A modern, secure, and scalable e-commerce module built with PHP 8+, featuring MVC architecture, dependency injection, and comprehensive security measures.

## 🚀 Features

### Core Functionality
- **Product Management**: Display product listings with pagination and detailed product pages
- **Shopping Cart**: Session-based cart with add, update, remove, and clear functionality
- **User Interface**: Clean, responsive design with mobile-first approach
- **Product Images**: Integration with Lorem Picsum for demo product images

### Security Features
- **CSRF Protection**: All forms protected against cross-site request forgery
- **Secure Sessions**: HTTPOnly cookies with session regeneration
- **Input Validation**: Comprehensive server-side validation with custom exceptions
- **SQL Injection Prevention**: Prepared statements throughout the application

### Technical Excellence
- **MVC Architecture**: Clean separation of concerns with controllers, models, and views
- **Service Layer**: Business logic abstracted into reusable services
- **Repository Pattern**: Data access layer with interface-based design
- **Dependency Injection**: Clean dependency management with container
- **Error Handling**: Custom exception hierarchy with structured error responses

## 🏗️ Architecture

### Directory Structure & Responsibilities
```
e-commerce-module/
├── bootstrap/app.php          # Application initialization & dependency injection setup
├── config/                    # Configuration management
│   ├── Database.php          # PDO singleton with connection pooling
│   ├── Session.php           # Secure session management
│   └── CSRF.php             # Cross-site request forgery protection
├── controllers/              # HTTP request handlers (entry point)
│   ├── ProductController.php # Product-related actions
│   └── CartController.php    # Shopping cart operations
├── database/schema.sql       # Database structure & sample data
├── exceptions/               # Custom error handling
│   ├── AppException.php      # Base exception class
│   └── ValidationException.php # Form validation errors
├── models/                   # Legacy data models (transitioning to repositories)
├── public/                   # Web-accessible assets
│   ├── css/style.css         # Responsive styling with modern design
│   └── images/              # Static image assets
├── repositories/             # Data access abstraction layer
│   ├── ProductRepositoryInterface.php
│   ├── ProductRepository.php # Product CRUD operations
│   ├── CartRepositoryInterface.php
│   └── CartRepository.php    # Cart data management
├── services/                 # Business logic layer
│   ├── ProductService.php    # Product business rules & validation
│   └── CartService.php       # Cart logic & calculations
└── views/                    # Presentation templates
    ├── product/              # Product-related views
    └── cart/                 # Shopping cart views
```

### Design Patterns & Implementation

#### **MVC Pattern Implementation**
```php
// Controller (HTTP Layer)
class ProductController {
    public function index() {
        $products = $this->productService->getProducts();
        include 'views/product/list.php'; // View
    }
}

// Service (Business Logic Layer)
class ProductService {
    public function getProducts() {
        return $this->productRepository->findAll(); // Model access
    }
}
```

#### **Repository Pattern**
```php
interface ProductRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?array;
}

class ProductRepository implements ProductRepositoryInterface {
    public function findAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM products");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

#### **Dependency Injection Container**
```php
// bootstrap/app.php
$container->set(PDO::class, function() {
    return new PDO('mysql:host=localhost;dbname=ecommerce_db');
});

$container->set(ProductRepositoryInterface::class, function($c) {
    return new ProductRepository($c->get(PDO::class));
});

$container->set(ProductService::class, function($c) {
    return new ProductService($c->get(ProductRepositoryInterface::class));
});
```

### Data Flow Architecture

```
HTTP Request → Controller → Service → Repository → Database
                      ↓
Response ← View ← Controller ← Service ← Repository ← Database
```

### Security Implementation

#### **CSRF Protection Flow**
1. **Token Generation**: Created per session in `CSRF::generateToken()`
2. **Form Injection**: Automatically added to all POST forms
3. **Validation**: Checked on every POST request in `index.php`
4. **Regeneration**: Token invalidated after successful use

#### **Session Security**
- HTTPOnly cookies prevent JavaScript access
- Secure flag for HTTPS enforcement
- Automatic regeneration every 30 minutes
- Session fixation protection

### Business Logic Implementation

#### **Product Management**
- **Validation**: Price > 0, name length, URL format checking
- **Caching**: Service-layer caching for performance
- **Error Handling**: Custom exceptions with meaningful messages

#### **Shopping Cart Logic**
- **Session-Based**: Cart persists across browser sessions
- **Quantity Management**: Add, update, remove with validation
- **Price Calculations**: Real-time total computation
- **Stock Checking**: Product availability verification

### Database Design

#### **Schema Overview**
```sql
-- Products table: Core product information
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart items: Session-based shopping cart
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_session_product (session_id, product_id)
);
```

#### **Indexing Strategy**
- **Primary Keys**: Auto-incrementing IDs
- **Foreign Keys**: Referential integrity
- **Unique Constraints**: Prevent duplicate cart items
- **Performance Indexes**: Created on frequently queried columns

## 📋 Requirements

- **PHP**: 8.0 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.0+
- **Web Server**: Apache/Nginx with mod_rewrite (optional)
- **Extensions**: PDO, PDO_MySQL

## 🚀 Installation

### 1. Clone or Download
```bash
git clone https://github.com/your-username/e-commerce-module.git
cd e-commerce-module
```

### 2. Database Setup
```bash
# Start MySQL service (XAMPP/WAMP)
# Create database
mysql -u root -p
CREATE DATABASE ecommerce_db;
exit;

# Import schema
mysql -u root -p ecommerce_db < database/schema.sql
```

### 3. Web Server Configuration
- Place the project in your web server's document root
- For Apache, ensure `.htaccess` support (if using URL rewriting)
- For Nginx, configure appropriate rewrite rules

### 4. Access Application
```
http://localhost/e-commerce-module/
```

## 📖 Usage

### Basic Navigation
- **Home/Product List**: Browse available products with pagination
- **Product Details**: Click "View Details" to see full product information
- **Add to Cart**: Use quantity controls and "Add to Cart" button
- **Cart Management**: View cart, update quantities, or remove items
- **Checkout**: Review cart summary (demo checkout flow)

### Key Features Demonstration
1. **Product Browsing**: Navigate through paginated product listings
2. **Cart Operations**: Add items, modify quantities, remove products
3. **Session Persistence**: Cart contents persist across browser sessions
4. **Security**: All forms include CSRF protection automatically

## 🛠️ API Reference

### URL Structure
```
GET  /e-commerce-module/index.php?action=index          # Product list
GET  /e-commerce-module/index.php?action=show&id={id}   # Product details
POST /e-commerce-module/index.php?action=addToCart      # Add item to cart
POST /e-commerce-module/index.php?action=updateCart     # Update cart item
POST /e-commerce-module/index.php?action=removeFromCart # Remove cart item
GET  /e-commerce-module/index.php?action=cart           # View cart
GET  /e-commerce-module/index.php?action=cartSummary    # Cart checkout
POST /e-commerce-module/index.php?action=clearCart      # Empty cart
```

### Form Data Examples

#### Add to Cart
```html
<form method="post" action="index.php?action=addToCart">
    <input type="hidden" name="csrf_token" value="generated_token">
    <input type="hidden" name="product_id" value="1">
    <input type="hidden" name="quantity" value="1">
    <button type="submit">Add to Cart</button>
</form>
```

#### Update Cart Item
```html
<form method="post" action="index.php?action=updateCart">
    <input type="hidden" name="csrf_token" value="generated_token">
    <input type="hidden" name="product_id" value="1">
    <input type="number" name="quantity" value="2" min="0">
    <button type="submit">Update</button>
</form>
```

## 🔧 Configuration

### Database Configuration (`config/Database.php`)
```php
private $host = 'localhost';
private $db_name = 'ecommerce_db';
private $username = 'your_username';
private $password = 'your_password';
```

### Session Configuration (`config/Session.php`)
```php
// Adjust session settings as needed
ini_set('session.cookie_secure', '1');     // Set to '0' for HTTP development
ini_set('session.gc_maxlifetime', '3600'); // Session lifetime in seconds
```

## 🧪 Testing

### Manual Testing Checklist
- [ ] Product listing displays correctly
- [ ] Product details page loads
- [ ] Add to cart functionality works
- [ ] Cart updates and removals function
- [ ] Session persistence across pages
- [ ] CSRF protection active on all forms
- [ ] Mobile responsiveness verified
- [ ] Form validation prevents invalid data

### Sample Test Data
The included `database/schema.sql` provides sample products:
- Laptop ($999.99)
- Smartphone ($699.99)
- Headphones ($199.99)
- Tablet ($399.99)
- Smart Watch ($299.99)

## 🔒 Security Features

### Implemented Protections
- **CSRF Tokens**: Generated per session, validated on all POST requests
- **Session Security**: HTTPOnly cookies, secure flags, regeneration
- **Input Sanitization**: All user inputs filtered and validated
- **SQL Injection Prevention**: Parameterized queries throughout
- **XSS Protection**: Output escaping on all dynamic content

### Security Headers (Recommended for Production)
```php
// Add to bootstrap/app.php or server configuration
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'');
```

## 🚀 Performance Optimizations

### Implemented Features
- **Database Indexing**: Optimized queries for product and cart operations
- **In-Memory Caching**: Service-layer caching for frequently accessed data
- **Lazy Loading**: Efficient data loading patterns
- **Prepared Statements**: Optimized database queries

### Production Recommendations
- Implement Redis for distributed caching
- Add CDN for static assets
- Configure database connection pooling
- Enable opcode caching (OPcache)

## 🤝 Contributing

### Development Setup
1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Make changes with proper documentation
4. Test thoroughly across different scenarios
5. Submit a pull request with detailed description

### Code Standards
- **PHP**: PSR-12 coding standards
- **Naming**: CamelCase for classes, snake_case for methods/variables
- **Documentation**: PHPDoc comments for all classes and methods
- **Security**: Input validation and sanitization on all user data
- **Testing**: Manual testing for all new features

### Commit Guidelines
```
feat: add new feature
fix: bug fix
docs: documentation update
style: code style changes
refactor: code refactoring
test: testing related changes
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **PHP Community**: For the robust language ecosystem
- **MySQL**: For reliable database functionality
- **Lorem Picsum**: For beautiful demo images
- **Open Source Community**: For inspiration and best practices

## 📞 Support

For questions, issues, or contributions:
- Create an issue in the repository
- Review existing documentation
- Check the troubleshooting section above

---

**Built with ❤️ using modern PHP practices and security-first development.**
