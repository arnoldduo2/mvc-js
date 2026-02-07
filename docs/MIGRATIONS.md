# Database Migrations Guide

## Overview

The MVC-JS framework includes a powerful, bidirectional database migration system that allows you to:

- **Create and manage database schema changes** through version-controlled migration files
- **Generate migrations from existing databases** (reverse engineering)
- **Detect and sync inconsistencies** between your database and migration files
- **Rollback changes** when needed

## Quick Start

### Creating a New Migration

```bash
php console make:migration create_products_table
```

This creates a new migration file in `src/database/migrations/` with a timestamp prefix.

### Running Migrations

```bash
php console migrate
```

Runs all pending migrations.

### Checking Migration Status

```bash
php console migrate:status
```

Shows which migrations have been run and which are pending.

## Migration File Structure

A migration file contains two methods:

```php
<?php

use App\App\Database\Migration;
use App\App\Database\Schema;
use App\App\Database\Blueprint;

class Migration_YYYYMMDDHHMMSS_description extends Migration
{
    public function up(): void
    {
        // Apply changes
        Schema::create('table_name', function (Blueprint $table) {
            // Define columns
        });
    }

    public function down(): void
    {
        // Revert changes
        Schema::dropIfExists('table_name');
    }
}
```

## Creating Tables

### Basic Table

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->integer('stock');
    $table->timestamps();
});
```

### Available Column Types

```php
$table->id();                           // Auto-incrementing BIGINT primary key
$table->string('name', 255);            // VARCHAR with length
$table->text('description');            // TEXT column
$table->longText('content');            // LONGTEXT column
$table->integer('count');               // INT column
$table->bigInteger('big_count');        // BIGINT column
$table->tinyInteger('small_count');     // TINYINT column
$table->boolean('is_active');           // TINYINT(1) column
$table->decimal('price', 8, 2);         // DECIMAL with precision and scale
$table->float('rating');                // FLOAT column
$table->double('precise_value');        // DOUBLE column
$table->date('birth_date');             // DATE column
$table->dateTime('created_at');         // DATETIME column
$table->timestamp('updated_at');        // TIMESTAMP column
$table->enum('status', ['active', 'inactive']); // ENUM column
$table->json('metadata');               // JSON column
```

### Column Modifiers

```php
$table->string('email')->nullable();              // Allow NULL
$table->string('status')->default('pending');     // Set default value
$table->integer('amount')->unsigned();            // Make unsigned
$table->timestamp('created_at')->useCurrent();    // DEFAULT CURRENT_TIMESTAMP
$table->timestamp('updated_at')->useCurrentOnUpdate(); // ON UPDATE CURRENT_TIMESTAMP
$table->string('note')->comment('User note');     // Add comment
```

### Timestamps and Soft Deletes

```php
$table->timestamps();              // Adds created_at and updated_at
$table->softDeletes();             // Adds deleted_at for soft deletes
```

### Indexes

```php
$table->primary('id');                    // Primary key
$table->unique('email');                  // Unique index
$table->index('status');                  // Regular index
$table->index(['user_id', 'product_id']); // Composite index
```

### Foreign Keys

```php
$table->foreign('user_id')
    ->references('id')
    ->on('users')
    ->onDelete('cascade')
    ->onUpdate('cascade');
```

## Modifying Tables

Create a migration to modify an existing table:

```bash
php console make:migration add_phone_to_users --table=users
```

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone', 20)->nullable()->after('email');
    });
}

public function down(): void
{
    Schema::dropColumn('users', 'phone');
}
```

## Reverse Engineering (Generate from Existing DB)

### Generate Migration for Specific Table

If you have a table created via MySQL interface:

```bash
php console migrate:generate --table=products
```

### Generate Migrations for All Tables

```bash
php console migrate:generate
```

This will create migration files for all existing tables (excluding the `migrations` table).

## Bidirectional Sync

### Detect Inconsistencies

Check if there are tables in your database without corresponding migration files:

```bash
php console migrate:sync
```

### Auto-Fix Inconsistencies

Generate missing migrations and mark them as executed:

```bash
php console migrate:sync --fix
```

This is perfect when you:

- Import an existing database
- Have tables created manually via MySQL interface
- Want to bring migration files in sync with your actual database

## Rolling Back Migrations

### Rollback Last Batch

```bash
php console migrate:rollback
```

### Rollback Multiple Batches

```bash
php console migrate:rollback --step=2
```

### Rollback All Migrations

```bash
php console migrate:reset
```

### Drop All Tables and Re-migrate

```bash
php console migrate:fresh
```

**Warning:** This will delete all data!

## Common Workflows

### Workflow 1: Starting a New Project

```bash
# Create a migration
php console make:migration create_users_table

# Edit the migration file
# Then run it
php console migrate
```

### Workflow 2: Working with Existing Database

```bash
# Generate migrations from existing tables
php console migrate:generate

# Sync and mark as executed
php console migrate:sync --fix

# Check status
php console migrate:status
```

### Workflow 3: Making Schema Changes

```bash
# Create migration for changes
php console make:migration add_status_to_orders --table=orders

# Edit the migration file
# Run the migration
php console migrate

# If something goes wrong, rollback
php console migrate:rollback
```

## Best Practices

1. **Never modify executed migrations** - Create a new migration instead
2. **Always test rollback** - Make sure your `down()` method works
3. **Use descriptive names** - `create_users_table`, `add_email_to_customers`
4. **One logical change per migration** - Don't combine unrelated changes
5. **Version control migrations** - Commit migration files to git
6. **Use foreign keys** - Maintain referential integrity
7. **Add indexes** - For columns used in WHERE clauses

## Example: Complete E-commerce Schema

```php
// Migration 1: Create users table
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('password');
    $table->string('name');
    $table->timestamps();
});

// Migration 2: Create products table
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->decimal('price', 10, 2);
    $table->integer('stock')->unsigned();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});

// Migration 3: Create orders table
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->bigInteger('user_id')->unsigned();
    $table->decimal('total', 10, 2);
    $table->enum('status', ['pending', 'processing', 'completed', 'cancelled']);
    $table->timestamps();

    $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');
});

// Migration 4: Create order_items table
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->bigInteger('order_id')->unsigned();
    $table->bigInteger('product_id')->unsigned();
    $table->integer('quantity')->unsigned();
    $table->decimal('price', 10, 2);
    $table->timestamps();

    $table->foreign('order_id')
        ->references('id')
        ->on('orders')
        ->onDelete('cascade');

    $table->foreign('product_id')
        ->references('id')
        ->on('products')
        ->onDelete('restrict');
});
```

## Troubleshooting

### Foreign Key Errors

If you get foreign key constraint errors, ensure:

- Referenced table exists
- Referenced column exists
- Column types match exactly
- Tables use InnoDB engine

### Migration Not Found

Make sure:

- Migration file is in `src/database/migrations/`
- Filename follows format: `YYYYMMDDHHMMSS_description.php`
- Class name matches: `Migration_YYYYMMDDHHMMSS_description`

### Permission Errors

Ensure the `src/database/migrations/` directory is writable:

```bash
chmod -R 755 src/database/migrations
```

## Advanced Features

### Raw SQL Execution

```php
public function up(): void
{
    Schema::raw("CREATE INDEX idx_custom ON users (LOWER(email))");
}
```

### Conditional Migrations

```php
public function up(): void
{
    if (!Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            // ...
        });
    }
}
```

### Check if Column Exists

```php
if (!Schema::hasColumn('users', 'phone')) {
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->nullable();
    });
}
```

## Command Reference

| Command                                             | Description                           |
| --------------------------------------------------- | ------------------------------------- |
| `php console migrate`                               | Run pending migrations                |
| `php console migrate --step=1`                      | Run one migration at a time           |
| `php console migrate:rollback`                      | Rollback last batch                   |
| `php console migrate:rollback --step=2`             | Rollback 2 batches                    |
| `php console migrate:reset`                         | Rollback all migrations               |
| `php console migrate:fresh`                         | Drop all tables and re-migrate        |
| `php console migrate:status`                        | Show migration status                 |
| `php console make:migration <name>`                 | Create new migration                  |
| `php console make:migration <name> --table=<table>` | Create migration for existing table   |
| `php console migrate:generate`                      | Generate migrations for all tables    |
| `php console migrate:generate --table=<table>`      | Generate migration for specific table |
| `php console migrate:sync`                          | Detect inconsistencies                |
| `php console migrate:sync --fix`                    | Generate missing migrations           |

---

**Happy Migrating! 🚀**
