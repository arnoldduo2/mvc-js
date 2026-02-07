# Helper Functions Guide

The framework includes several global helper functions to simplify common tasks in your controllers and views.

## View & Response Helpers

### `view(string $view, array $data = [])`

Renders a view or returns a JSON response depending on the request type (SPA vs Full Page).

**Arguments:**

- `$view`: The name of the view file (looks in `src/resources/views/pages/`). Dot notation is supported (e.g., `users.index`).
- `$data`: An array of data to pass to the view.

**Usage:**

```php
// In Controller
public function index()
{
    // Automatically renders pages/home.php with the layout
    return view('home', ['title' => 'Welcome']);
}
```

### `__includes(string $name, array $data = [])`

Includes a partial view from the `src/resources/views` directory.

**Arguments:**

- `$name`: The path to the partial view.
- `$data`: Data to pass to the partial.

**Usage:**

```php
<!-- In a layout or view -->
<?php __includes('layouts.header'); ?>
```

### `useSpa()`

Generates the necessary HTML script tags to enable the Single Page Application (SPA) system. This should be placed in the `<head>` of your main layout.

**Usage:**

```php
<!-- src/resources/views/app.php -->
<head>
    <?= useSpa() ?>
</head>
```

### `addAssets(array $assets, string $type = 'css')`

Generates HTML tags for including CSS or JS assets.

**Arguments:**

- `$assets`: Array of asset paths (relative to public root or mapped via .htaccess).
- `$type`: 'css' or 'js'.

**Usage:**

```php
<?= addAssets(['css/custom.css'], 'css') ?>
```

### `isAjaxRequest()`

Checks if the current request is an AJAX/SPA request.

**Returns:** `bool`

**Usage:**

```php
if (isAjaxRequest()) {
    // Return JSON
} else {
    // Return HTML
}
```

## Other Core Helpers

### `url(string $path = '')`

Generates a fully qualified URL to the given path.

```php
<a href="<?= url('/users') ?>">Users</a>
```

### `env(string $key, mixed $default = null)`

Gets an environment variable from the `.env` file.

```php
$debug = env('APP_DEBUG', false);
```

### `csrf_token()`

Returns the current CSRF token.

```php
<input type="hidden" name="_token" value="<?= csrf_token() ?>">
```

### `csrf_field()`

Generates a hidden input field with the CSRF token.

```php
<?= csrf_field() ?>
```

### `old(string $key, mixed $default = '')`

Retreives old input data from the previous request (useful for form validation).

```php
<input type="text" name="email" value="<?= old('email') ?>">
```
