<?php

declare(strict_types=1);

namespace App\App\Models;

use App\App\Core\Model;

/**
 * User Model
 */
class User extends Model
{
    /**
     * Table name
     */
    protected static string $table = 'users';

    /**
     * Fillable attributes
     */
    protected static array $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'bio',
        'role',
        'is_active',
        'created_by', // Optional fields from Model base
        'updated_by'
    ];

    /**
     * Guarded attributes
     */
    protected static array $guarded = ['id'];

    /**
     * Enable soft deletes (disabled for demo/testing)
     */
    protected static bool $softDeletes = false;
}
