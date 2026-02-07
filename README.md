# MVC-JS Framework

A modern PHP MVC framework with Single Page Application (SPA) capabilities, Laravel-style routing, and streamlined architecture.

![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ Features

- **🚀 Modern Router** - Laravel-style fluent API with middleware support
- **💾 Streamlined Model** - Clean query builder with mass assignment protection
- **🗄️ Database Migrations** - Bidirectional migrations with reverse engineering
- **⚡ SPA System** - Server-side rendering with client-side navigation
- **🔒 Error Handler** - Comprehensive error handling with AJAX/SPA support
- **🎨 Dark Mode** - Built-in dark mode support with persistence
- **📦 Modular Architecture** - Clean separation of concerns
- **🔐 Middleware Pipeline** - Authentication, permissions, and custom middleware
- **🌐 Base Path Support** - Works in subdirectories and production
- **⚙️ CLI Console** - Powerful command-line tools for migrations and more

## 📋 Requirements

- PHP 8.0 or higher
- Composer
- Apache/Nginx with mod_rewrite
- MySQL/MariaDB (or other PDO-supported database)

## 🚀 Quick Start

### Installation

```bash
# Clone the repository
git clone https://github.com/arnoldduo2/mvc-js.git
cd mvc-js

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Configure your database in .env
```

### Configuration

Edit `.env` file:

```env
APP_NAME=MVC-JS
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/mvc-js

DB_ENG=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=mvc_js
DB_USER=root
DB_PASS=
```

### Apache Configuration

Ensure `.htaccess` is enabled and `mod_rewrite` is active.

### Run the Application

```bash
# Using PHP built-in server
php -S localhost:8000

# Or configure Apache virtual host
```

Visit `http://localhost:8000` in your browser.

## 📚 Documentation

- [Architecture Guide](ARCHITECTURE.md) - System architecture and design patterns
- [Controller Guide](CONTROLLER.md) - Base controller methods and usage examples
- [Router Guide](docs/ROUTER.md) - Laravel-style routing documentation
- [Model Guide](docs/MODEL.md) - Database and ORM usage
- **[Migration Guide](MIGRATIONS.md) - Database migrations and schema management** ⭐ NEW
- [SPA Guide](docs/SPA.md) - Single Page Application implementation
- [Error Handler](docs/ERROR_HANDLER.md) - Error handling system

## 🏗️ Project Structure

```
mvc-js/
├── src/
│   ├── App/
│   │   ├── Console/          # CLI commands
│   │   │   ├── Commands/    # Migration commands
│   │   │   ├── Command.php
│   │   │   └── ConsoleKernel.php
│   │   ├── Controllers/      # Application controllers
│   │   ├── Core/             # Core framework classes
│   │   │   ├── Application.php
│   │   │   ├── Router.php
│   │   │   ├── Model.php
│   │   │   ├── View.php
│   │   │   └── Middleware/
│   │   ├── Database/         # Database utilities
│   │   │   ├── Migration.php
│   │   │   ├── Schema.php
│   │   │   ├── Blueprint.php
│   │   │   ├── MigrationManager.php
│   │   │   ├── SchemaInspector.php
│   │   │   └── MigrationGenerator.php
│   │   └── Models/           # Application models
│   ├── Helpers/              # Helper classes and functions
│   ├── config/               # Configuration files
│   ├── resources/            # Frontend resources
│   │   ├── css/             # Stylesheets
│   │   ├── js/              # JavaScript (ES6 modules)
│   │   └── views/           # View templates
│   ├── routes/              # Route definitions
│   │   ├── web.php
│   │   └── api.php
│   └── storage/             # Logs, cache, uploads
├── database/
│   └── migrations/          # Database migration files
├── vendor/                  # Composer dependencies
├── console                  # CLI entry point
├── .htaccess               # Apache rewrite rules
├── index.php               # Application entry point
└── composer.json           # Dependencies
```

## 🎯 Core Features

### Laravel-Style Routing

```php
// Basic routes
Router::get('/users', [UserController::class, 'index']);
Router::post('/users', [UserController::class, 'store']);

// Routes with middleware
Router::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth');

// Route groups
Router::group(['prefix' => 'api', 'middleware' => 'auth'], function() {
    Router::get('/users', [UserController::class, 'index']);
    Router::post('/users', [UserController::class, 'store']);
});

// Named routes
Router::get('/profile', [ProfileController::class, 'show'])
    ->name('profile.show')
    ->middleware('auth');
```

### Streamlined Model

```php
// Query builder
$users = User::query()
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Mass assignment
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Relationships (coming soon)
$user->posts()->get();
```

### Database Migrations ⭐ NEW

Powerful bidirectional migration system with reverse engineering capabilities.

```bash
# Create a new migration
php console make:migration create_products_table

# Run pending migrations
php console migrate

# Rollback last batch
php console migrate:rollback

# Check migration status
php console migrate:status

# Generate migrations from existing database
php console migrate:generate --table=users

# Sync database with migrations (detect inconsistencies)
php console migrate:sync --fix
```

```php
// Example migration file
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2)->unsigned();
    $table->integer('stock')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    // Indexes
    $table->unique('sku');
    $table->index('name');

    // Foreign keys
    $table->foreign('category_id')
        ->references('id')
        ->on('categories')
        ->onDelete('cascade');
});
```

**Key Features:**

- ✅ Forward & reverse migrations
- ✅ Generate from existing database tables
- ✅ Detect schema inconsistencies
- ✅ Transaction-safe execution
- ✅ Fluent schema builder
- ✅ Full CLI support

See [Migration Guide](MIGRATIONS.md) for complete documentation.

### SPA System

```php
// Controller
public function index()
{
    return View::page('pages/home', [
        'title' => 'Home',
        'users' => User::all()
    ]);
}
```

```javascript
// JavaScript automatically handles navigation
// No page reloads!
```

### Error Handling

- Automatic AJAX/SPA detection
- JSON responses for SPA requests
- HTML error pages for regular requests
- Comprehensive logging
- Debug mode support

## 🎨 Dark Mode

Toggle dark mode using the button in the navbar. Preference is saved to localStorage.

```javascript
// Programmatically toggle
document.body.classList.toggle("dark-mode");
```

## 🤝 Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Workflow

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 👥 Authors

- **Arnold Tinashe Samhungu** - _Initial work_ - [@arnoldduo2](https://github.com/arnoldduo2)

See also the list of [contributors](AUTHORS.md) who participated in this project.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Laravel for routing inspiration
- Modern PHP practices and design patterns
- ES6 modules for clean JavaScript architecture
- Anode Error Handler package

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/arnoldduo2/mvc-js/issues)
- **Email**: arnoldduo2@gmail.com

## 🗺️ Roadmap

- [x] **Database migrations system** ✅ COMPLETED
- [x] **CLI tool for migrations** ✅ COMPLETED
- [ ] Model relationships (hasMany, belongsTo, etc.)
- [ ] Authentication scaffolding
- [ ] Database seeders
- [ ] API rate limiting
- [ ] WebSocket support
- [ ] Unit testing framework integration

---

Made with ❤️ by Arnold Tinashe Samhungu
