# Base Controller Methods Guide

The enhanced base Controller class provides comprehensive helper methods for common controller tasks. All controllers that extend `App\App\Core\Controller` have access to these methods.

## View & Response Methods

### `view(string $view, array $data = [])`

Render a view with automatic SPA detection.

```php
$this->view('pages/dashboard', ['title' => 'Dashboard']);
```

### `json(array $data, int $statusCode = 200)`

Return a JSON response.

```php
$this->json(['message' => 'Success'], 200);
```

### `success(string $message, array $data = [], int $statusCode = 200)`

Return a standardized success JSON response.

```php
$this->success('User created successfully', ['user' => $user]);
```

### `error(string $message, array $errors = [], int $statusCode = 400)`

Return a standardized error JSON response.

```php
$this->error('Validation failed', ['email' => 'Invalid email'], 422);
```

## Redirect Methods

### `redirect(string $url, int $code = 302)`

Redirect to a URL.

```php
$this->redirect(url('/dashboard'));
```

### `back(string $fallback = '/')`

Redirect back to the previous page.

```php
$this->back();
```

### `redirectWith(string $url, string $message, string $type = 'success')`

Redirect with a flash message.

```php
$this->redirectWith(url('/dashboard'), 'Profile updated!', 'success');
```

## Request Methods

### `input(string $key, mixed $default = null)`

Get a single input value from POST or GET.

```php
$email = $this->input('email');
$page = $this->input('page', 1);
```

### `all()`

Get all request inputs (merged GET and POST).

```php
$data = $this->all();
```

### `only(array $keys)`

Get only specified inputs.

```php
$credentials = $this->only(['email', 'password']);
```

### `except(array $keys)`

Get all inputs except specified keys.

```php
$data = $this->except(['_token', 'password_confirmation']);
```

### `has(string $key)`

Check if input exists.

```php
if ($this->has('email')) {
    // ...
}
```

### `filled(string $key)`

Check if input exists and is not empty.

```php
if ($this->filled('search')) {
    // ...
}
```

### `isPost()`, `isGet()`, `method()`

Check request method.

```php
if ($this->isPost()) {
    // Handle POST request
}
```

## Validation Methods

### `validate(array $rules, ?array $data = null)`

Validate request data and return a Validator instance.

```php
$validator = $this->validate([
    'email' => 'required|email',
    'password' => 'required|min:8'
]);

if ($validator->fails()) {
    $errors = $validator->errors();
}
```

### `validateOrFail(array $rules, ?array $data = null)`

Validate and automatically redirect back with errors on failure.

```php
$validated = $this->validateOrFail([
    'name' => 'required|min:3',
    'email' => 'required|email'
]);
// If validation fails, user is redirected back with errors
// If successful, $validated contains the validated data
```

### `verifyCsrf(?array $data = null)`

Verify CSRF token.

```php
if ($this->verifyCsrf()) {
    // Token is valid
}
```

### `verifyCsrfOrFail(?array $data = null)`

Verify CSRF token or redirect back with error.

```php
$this->verifyCsrfOrFail();
// Continues only if CSRF token is valid
```

## Session Methods

### `session(string $key, mixed $default = null)`

Get a session value.

```php
$userId = $this->session('user_id');
```

### `setSession(string $key, mixed $value)`

Set a session value.

```php
$this->setSession('user_id', 123);
```

### `hasSession(string $key)`

Check if session key exists.

```php
if ($this->hasSession('cart')) {
    // ...
}
```

### `forgetSession(string $key)`

Remove a session key.

```php
$this->forgetSession('temp_data');
```

## Flash Message Methods

### `flash(string $type, string $message)`

Set a flash message (success, error, warning, info).

```php
$this->flash('success', 'Profile updated successfully!');
```

### `getFlash(string $type)`

Get and remove a flash message.

```php
$message = $this->getFlash('success');
```

### `hasFlash(string $type)`

Check if a flash message exists.

```php
if ($this->hasFlash('error')) {
    // ...
}
```

## File Upload Methods

### `hasFile(string $key)`

Check if a file was uploaded.

```php
if ($this->hasFile('avatar')) {
    // ...
}
```

### `file(string $key)`

Get uploaded file information.

```php
$file = $this->file('avatar');
// Returns: ['name' => '...', 'type' => '...', 'tmp_name' => '...', 'size' => ...]
```

### `moveFile(string $key, string $destination, ?string $filename = null)`

Move uploaded file to destination.

```php
$path = $this->moveFile('avatar', 'public/uploads/avatars');
// Or with custom filename:
$path = $this->moveFile('avatar', 'public/uploads/avatars', 'user_123.jpg');
```

### `validateFile(string $key, array $rules = [])`

Validate uploaded file.

```php
$errors = $this->validateFile('avatar', [
    'maxSize' => 2097152, // 2MB in bytes
    'allowedTypes' => ['image/jpeg', 'image/png', 'image/gif']
]);

if (empty($errors)) {
    // File is valid
}
```

## Authorization Methods

### `isAuthenticated()`

Check if user is authenticated.

```php
if ($this->isAuthenticated()) {
    // User is logged in
}
```

### `userId()`

Get authenticated user ID.

```php
$userId = $this->userId();
```

### `user()`

Get authenticated user data.

```php
$user = $this->user();
```

### `requireAuth(string $redirectTo = '/login')`

Require authentication or redirect.

```php
public function dashboard(): void
{
    $this->requireAuth();
    // Only authenticated users reach here
}
```

### `requireGuest(string $redirectTo = '/')`

Require guest (not authenticated) or redirect.

```php
public function login(): void
{
    $this->requireGuest();
    // Only guests (non-authenticated users) reach here
}
```

## Utility Methods

### `abort(int $code = 404, string $message = '')`

Abort with an error page.

```php
$this->abort(404, 'Page not found');
$this->abort(403, 'Unauthorized access');
```

### `paginate(int $total, int $perPage = 15, ?int $currentPage = null)`

Generate pagination data.

```php
$pagination = $this->paginate(150, 20);
// Returns:
// [
//     'total' => 150,
//     'per_page' => 20,
//     'current_page' => 1,
//     'total_pages' => 8,
//     'offset' => 0,
//     'has_more' => true,
//     'has_previous' => false
// ]

// Use with database queries:
$users = User::limit($pagination['per_page'])
    ->offset($pagination['offset'])
    ->get();
```

## Example Controller

Here's a complete example showing many of these methods in action:

```php
<?php

namespace App\App\Controllers;

use App\App\Core\Controller;
use App\App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $search = $this->input('search', '');
        $users = User::where('name', 'LIKE', "%{$search}%")->get();

        $this->view('users/index', [
            'title' => 'Users',
            'users' => $users,
            'search' => $search
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('users/create', ['title' => 'Create User']);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->verifyCsrfOrFail();

        $validated = $this->validateOrFail([
            'name' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|password|confirmed'
        ]);

        // Handle avatar upload
        if ($this->hasFile('avatar')) {
            $errors = $this->validateFile('avatar', [
                'maxSize' => 2097152,
                'allowedTypes' => ['image/jpeg', 'image/png']
            ]);

            if (!empty($errors)) {
                $this->flash('error', implode(', ', $errors));
                $this->back();
            }

            $avatarPath = $this->moveFile('avatar', 'public/uploads/avatars');
            $validated['avatar'] = $avatarPath;
        }

        $user = User::create($validated);

        $this->redirectWith(
            url('/users'),
            'User created successfully!',
            'success'
        );
    }

    public function apiIndex(): void
    {
        $this->requireAuth();

        $page = $this->input('page', 1);
        $perPage = 20;

        $total = User::count();
        $pagination = $this->paginate($total, $perPage, $page);

        $users = User::limit($pagination['per_page'])
            ->offset($pagination['offset'])
            ->get();

        $this->success('Users retrieved', [
            'users' => $users,
            'pagination' => $pagination
        ]);
    }
}
```
