# Architecture Documentation

## Overview

MVC-JS is a modern PHP framework built with clean architecture principles, following MVC (Model-View-Controller) pattern with SPA (Single Page Application) capabilities.

## Design Principles

### 1. Separation of Concerns

- **Models**: Data layer and business logic
- **Views**: Presentation layer
- **Controllers**: Request handling and coordination
- **Core**: Framework infrastructure

### 2. Single Responsibility

Each class has one clear purpose and responsibility.

### 3. Dependency Injection

Dependencies are injected rather than hard-coded.

### 4. DRY (Don't Repeat Yourself)

Reusable components and helpers eliminate code duplication.

## System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                      HTTP Request                        │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                    .htaccess                             │
│            (URL Rewriting to index.php)                  │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                   index.php                              │
│         (Bootstrap & Application Entry)                  │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                 Application::boot()                      │
│  ┌──────────────────────────────────────────────────┐   │
│  │ 1. Load System Files                             │   │
│  │ 2. Initialize Error Handler                      │   │
│  │ 3. Load Routes (web.php, api.php)                │   │
│  └──────────────────────────────────────────────────┘   │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                  Router::dispatch()                      │
│  ┌──────────────────────────────────────────────────┐   │
│  │ 1. Parse Request URI                             │   │
│  │ 2. Match Route                                   │   │
│  │ 3. Execute Middleware Pipeline                   │   │
│  │ 4. Call Controller Action                        │   │
│  └──────────────────────────────────────────────────┘   │
└──────────────────────┬──────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────┐
│                    Controller                            │
│  ┌──────────────────────────────────────────────────┐   │
│  │ 1. Process Request                               │   │
│  │ 2. Interact with Models                          │   │
│  │ 3. Return View/JSON Response                     │   │
│  └──────────────────────────────────────────────────┘   │
└──────────────────────┬──────────────────────────────────┘
                       │
        ┌──────────────┴──────────────┐
        │                             │
        ▼                             ▼
┌──────────────┐            ┌──────────────────┐
│  AJAX/SPA    │            │  Regular Request │
│  Request     │            │                  │
└──────┬───────┘            └────────┬─────────┘
       │                             │
       ▼                             ▼
┌──────────────┐            ┌──────────────────┐
│ JSON Response│            │  HTML Response   │
│ {            │            │  (Full Page)     │
│   content,   │            │                  │
│   title,     │            │                  │
│   scripts    │            │                  │
│ }            │            │                  │
└──────────────┘            └──────────────────┘
```

## Core Components

### 1. Application Class

**Location**: `src/App/Core/Application.php`

**Responsibilities**:

- Bootstrap the application
- Initialize error handling
- Load configuration
- Load routes
- Dispatch requests

**Pattern**: Singleton

```php
$app = Application::getInstance();
$app->boot()->run();
```

### 2. Router System

**Location**: `src/App/Core/Router.php`

**Features**:

- Laravel-style fluent API
- Middleware pipeline
- Route groups
- Named routes
- Parameter constraints
- Base path support

**Design Pattern**: Facade + Builder

```php
Router::get('/users/{id}', [UserController::class, 'show'])
    ->middleware('auth')
    ->where('id', '[0-9]+')
    ->name('users.show');
```

### 3. Model Layer

**Location**: `src/App/Core/Model.php`

**Features**:

- Query Builder with fluent API
- Mass assignment protection
- Automatic timestamps
- Soft deletes support
- Type hinting

**Design Pattern**: Active Record + Query Builder

```php
User::query()
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->get();
```

### 4. View System

**Location**: `src/App/Core/View.php`

**Features**:

- Template rendering
- JSON responses for SPA
- Layout support
- Data passing

**Design Pattern**: Template View

```php
View::page('pages/home', [
    'title' => 'Home',
    'data' => $data
]);
```

### 5. Middleware Pipeline

**Location**: `src/App/Core/Middleware/`

**Available Middleware**:

- `AuthMiddleware` - Authentication
- `PermissionMiddleware` - Permission checking
- `RoleMiddleware` - Role-based access

**Design Pattern**: Chain of Responsibility

```php
Router::get('/admin', [AdminController::class, 'index'])
    ->middleware(['auth', 'role:admin']);
```

### 6. Error Handler

**Location**: `vendor/anode/error-handler/`

**Features**:

- AJAX/SPA detection
- JSON error responses
- HTML error pages
- Comprehensive logging
- Debug mode support

**Integration**: Initialized in `Application::boot()`

## Request Lifecycle

### 1. Entry Point

```
HTTP Request → .htaccess → index.php
```

### 2. Bootstrap

```php
// Load autoloader
require __DIR__ . '/vendor/autoload.php';

// Load constants
require __DIR__ . '/src/config/constants.php';

// Bootstrap application
require __DIR__ . '/src/boot/app.php';
```

### 3. Application Boot

```php
Application::getInstance()
    ->boot()  // Initialize system
    ->run();  // Handle request
```

### 4. Route Matching

```
Router::dispatch()
  → Parse URI
  → Strip base path
  → Match route pattern
  → Extract parameters
```

### 5. Middleware Execution

```
Request → Middleware 1 → Middleware 2 → Controller
```

### 6. Controller Action

```php
public function index()
{
    $data = Model::all();
    return View::page('pages/index', compact('data'));
}
```

### 7. Response

**Regular Request**:

```html
<!DOCTYPE html>
<html>
  <!-- Full HTML page -->
</html>
```

**AJAX/SPA Request**:

```json
{
  "type": "html",
  "content": "<div>...</div>",
  "title": "Page Title",
  "scripts": ["console.log('loaded');"]
}
```

## SPA Architecture

### Client-Side (JavaScript)

**Location**: `src/resources/js/`

**Components**:

- `app.js` - Main application
- `app-router.js` - Client-side router

**Flow**:

```
Link Click
  → Intercept
  → Fetch JSON
  → Update DOM
  → Update History
  → Execute Scripts
```

### Server-Side (PHP)

**Detection**:

```php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    // Return JSON
} else {
    // Return HTML
}
```

**Response Format**:

```php
View::page('pages/home', $data);
// Returns JSON for AJAX, HTML for regular requests
```

## Database Layer

### Connection Management

**Pattern**: Singleton per connection

```php
class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            self::$connection = new PDO(/* ... */);
        }
        return self::$connection;
    }
}
```

### Query Builder

**Pattern**: Fluent Interface

```php
$query = Model::query()
    ->select(['id', 'name'])
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->limit(10);

$results = $query->get();
```

## Security

### 1. Input Validation

- Type hinting
- Parameter constraints
- Mass assignment protection

### 2. Authentication

- Middleware-based
- Session management
- Token support (API)

### 3. Authorization

- Role-based access control
- Permission checking
- Middleware pipeline

### 4. Error Handling

- Production mode hides details
- Development mode shows stack traces
- Comprehensive logging

## Performance Optimization

### 1. Autoloading

- PSR-4 autoloading via Composer
- Lazy loading of classes

### 2. Caching

- Opcode caching (OPcache)
- Route caching (planned)
- View caching (planned)

### 3. Database

- Connection pooling
- Query optimization
- Prepared statements

### 4. Frontend

- ES6 modules
- Minimal JavaScript
- CSS optimization

## Extensibility

### Adding Middleware

```php
// 1. Create middleware class
class CustomMiddleware implements MiddlewareInterface {
    public function handle($request, Closure $next) {
        // Before logic
        $response = $next($request);
        // After logic
        return $response;
    }
}

// 2. Register in Router
Router::$middlewareAliases['custom'] = CustomMiddleware::class;

// 3. Use in routes
Router::get('/path', $action)->middleware('custom');
```

### Adding Helpers

```php
// src/Helpers/helpers.php
function myHelper($param) {
    return /* ... */;
}
```

### Creating Models

```php
namespace App\App\Models;

use App\App\Core\Model;

class MyModel extends Model {
    protected string $table = 'my_table';
    protected array $fillable = ['field1', 'field2'];
}
```

## Best Practices

1. **Use Type Hints**: Always declare types for parameters and return values
2. **Follow PSR Standards**: PSR-4 autoloading, PSR-12 coding style
3. **Dependency Injection**: Inject dependencies rather than creating them
4. **Single Responsibility**: One class, one purpose
5. **DRY Principle**: Reuse code through helpers and traits
6. **Security First**: Validate input, escape output, use prepared statements
7. **Error Handling**: Use try-catch blocks and proper error responses

## Testing Strategy

### Unit Tests (Planned)

- Test individual classes
- Mock dependencies
- Use PHPUnit

### Integration Tests (Planned)

- Test component interactions
- Database integration
- API endpoints

### Browser Tests (Planned)

- Test SPA navigation
- Test user flows
- Use Selenium/Playwright

---

**Last Updated**: 2026-02-06  
**Version**: 1.0.0
