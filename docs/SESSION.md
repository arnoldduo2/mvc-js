# Session Management

The `App\App\Core\Session` class provides a robust way to manage session data, featuring both modern methods and legacy compatibility.

## Basic Usage

You can access session data using the `Session` class or the `session()` helper function.

### Setting Data

```php
use App\App\Core\Session;

// Using static method
Session::put('key', 'value');

// Using helper
session()->put('key', 'value');
// or
session('key', 'value'); // Note: 'session(key, val)' helper signature does NOT set. It retrieves.
// To set via helper, you must get the instance:
session()->put('key', 'value');
```

> **Note:** The `session($key, $default)` helper is primarily for _retrieving_ data. To _set_ data, call methods on the instance returned by `session()`.

### Retrieving Data

```php
// Get value
$value = Session::get('key');

// Get with default
$value = Session::get('key', 'default_value');

// Using helper
$value = session('key', 'default_value');
```

### Checking Existence

```php
if (Session::has('key')) {
    // ...
}
```

### Deleting Data

```php
// Remove single item
Session::forget('key');

// Clear all session data
Session::flush();
```

## Legacy Compatibility

The class supports methods compatible with the legacy `Session` class to ease migration.

| Legacy Method                   | New Equivalent          | Description           |
| :------------------------------ | :---------------------- | :-------------------- |
| `Session::set_userData($k, $v)` | `Session::put($k, $v)`  | Set session data      |
| `Session::get_userData($k)`     | `Session::get($k)`      | Get session data      |
| `Session::set_settings($k, $v)` | `Session::put($k, $v)`  | Set settings          |
| `Session::get_settings($k)`     | `Session::get($k)`      | Get settings          |
| `Session::csfrToken()`          | `CSRF::getToken()`      | Get CSRF token        |
| `Session::regen()`              | `Session::regenerate()` | Regenerate Session ID |

## Security

- **Regeneration:** Use `Session::regenerate()` to prevent session fixation.
- **CSRF:** CSRF tokens are managed via `App\App\Core\CSRF` but accessible via `Session::csfrToken()` for compatibility.

## Helper Reference

`session(?string $key = null, mixed $default = null)`

- If `$key` is null, returns the `Session` instance.
- If `$key` is provided, returns the session value or `$default`.
