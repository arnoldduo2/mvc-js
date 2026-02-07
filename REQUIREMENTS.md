# System Requirements Checker

MVC-JS includes a built-in System Requirements Checker to ensure your server environment meets the necessary criteria for the application to run smoothly.

## 🚀 Features

- **Configurable**: Define required PHP version, extensions, and functions in a simple config file.
- **CLI Command**: Check requirements from the command line.
- **Web Interface**: View requirements status in the browser.
- **Boot Enforcement**: Automatically halts application execution if requirements are not met.

## ⚙️ Configuration

The configuration file is located at `src/config/requirements.php`.

```php
return [
    // Minimum PHP version
    'php' => '8.0.0',

    // Required Environment Variables
    'env' => [
        'DB_ENG',
        'DB_HOST',
        'DB_NAME',
        'DB_USER',
        'DB_PASS',
    ],

    // Required PHP extensions
    'extensions' => [
        'pdo',
        'mbstring',
        'json',
        // ...
    ],

    // Required PHP functions (must not be disabled)
    'functions' => [
        'exec',
        'shell_exec',
        // ...
    ],
];
```

## 🗄️ Database Support

The application supports both **MySQL** and **SQLite**. You can configure this in your `.env` file.

### MySQL (Default)

```ini
DB_ENG=mysql
DB_HOST=127.0.0.1
DB_NAME=mvc_js
DB_USER=root
DB_PASS=
```

### SQLite

```ini
DB_ENG=sqlite
DB_NAME=/absolute/path/to/database.sqlite
```

## 🛠️ Usage

### CLI Command

Run the following command in your terminal to check requirements:

```bash
php console requirements:check
```

**Output Example:**

```text
System Requirements Check
----------------------------------------
PHP Version                    [PASS]

PHP Extensions:
pdo                            [PASS]
mbstring                       [PASS]
json                           [PASS]
...

----------------------------------------
All requirements met successfully!
```

### Web Interface

You can view the requirements status by visiting:

`http://your-app-url/requirements`

### Boot Enforcement

The application automatically checks requirements when it boots (`Application::boot`).

- If **all requirements are met**, the application runs normally.
- If **any requirement is missing**:
  - **Web**: The application halts with a **500 Internal Server Error** and displays a detailed HTML report of the missing requirements.
  - **CLI**: The command halts with an error message instructing you to run `php console requirements:check`.
