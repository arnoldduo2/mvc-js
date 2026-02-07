# Service Providers & Dependency Injection

The framework includes a powerful Service Container and Service Provider system for managing class dependencies and performing dependency injection.

## Service Container

The Service Container is a tool for managing class dependencies and performing dependency injection in your application.

### Binding

You can bind interfaces to implementations using the `bind` method:

```php
$container->bind(UserRepositoryInterface::class, UserRepository::class);
```

### Singletons

You can bind a class as a singleton, meaning it will only be resolved once:

```php
$container->singleton(DatabaseService::class, function () {
    return new DatabaseService(...);
});
```

### Resolution

You can resolve a class instance from the container using `make`:

```php
$userRepository = $container->make(UserRepositoryInterface::class);
```

Automatic dependency injection is supported for classes resolved via the container.

---

## Service Providers

Service providers are the central place of all application bootstrapping. Your own application, as well as all of MVC-JS's core services, are bootstrapped via providers.

### Writing Service Providers

All service providers extend the `App\App\Core\ServiceProvider` class. They contain a `register` method and a `boot` method.

#### The Register Method

In the `register` method, you should only bind things into the service container. You should simply bind a service to the container.

```php
namespace App\App\Providers;

use App\App\Core\ServiceProvider;
use App\App\Services\EmailService;

class EmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService(env('MAIL_API_KEY'));
        });
    }
}
```

#### The Boot Method

This method is called after all other service providers have been registered. This allows you to access all other services that have been registered by the framework.

```php
public function boot(): void
{
    // Perform post-registration booting
}
```

### Registering Providers

All service providers are registered in the `src/config/providers.php` configuration file:

```php
return [
    App\App\Providers\AppServiceProvider::class,
    App\App\Providers\EmailServiceProvider::class,
];
```

---

## Dependency Injection in Controllers

The framework automatically resolves controllers via the Service Container. This means you can type-hint dependencies in your controller's constructor, and they will be automatically injected.

```php
namespace App\App\Controllers;

use App\App\Core\Controller;
use App\App\Services\UserService;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAll();
        // ...
    }
}
```
