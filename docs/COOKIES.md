# Cookie Management

The `App\App\Core\Cookie` class provides a fluent interface for managing cookies with secure defaults.

## Configuration

Cookie settings are defined in `src/config/cookie.php`.

```php
return [
    'lifetime' => 120,      // Minutes
    'path' => '/',
    'domain' => null,       // Current domain
    'secure' => false,      // Set to true in production (HTTPS)
    'httpOnly' => true,     // Prevent JS access
    'sameSite' => 'Lax',    // CSRF protection
];
```

## Basic Usage

You can access cookies using the `Cookie` class or the `cookie()` helper function.

### Setting Cookies

```php
use App\App\Core\Cookie;

// Set cookie (uses default config for duration, path, etc)
Cookie::set('name', 'value');

// Custom duration (minutes)
Cookie::set('name', 'value', 60);

// Forever cookie (5 years)
Cookie::forever('name', 'value');
```

### Retrieving Cookies

```php
// Get value
$value = Cookie::get('name');

// Get with default
$value = Cookie::get('name', 'default_value');

// Using helper
$value = cookie('name', 'default_value');
```

### Checking Existence

```php
if (Cookie::has('name')) {
    // ...
}
```

### Deleting Cookies

```php
// Remove cookie (sets expiration in the past)
Cookie::forget('name');
```

## Helper Reference

`cookie(?string $key = null, mixed $default = null)`

- If `$key` is null, returns the `Cookie` instance.
- If `$key` is provided, returns the cookie value or `$default`.

Example:

```php
// Get instance to set cookie
cookie()->set('theme', 'dark');

// Get value
$theme = cookie('theme');
```
