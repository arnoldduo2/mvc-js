<?php

declare(strict_types=1);

use App\App\Database\Migration;
use App\App\Database\Schema;
use App\App\Database\Blueprint;

/**
 * Example Migration: Create Users Table
 * 
 * This is an example migration showing how to create a table with various column types.
 */
class Migration_20260206210000_create_users_table extends Migration
{
   /**
    * Run the migration
    */
   public function up(): void
   {
      Schema::create('users', function (Blueprint $table) {
         // Primary key
         $table->id();

         // Basic columns
         $table->string('name', 100);
         $table->string('email');
         $table->unique('email');
         $table->string('password');

         // Optional columns
         $table->string('phone', 20)->nullable();
         $table->text('bio')->nullable();

         // Enum column
         $table->enum('role', ['admin', 'user', 'guest'])->default('user');

         // Boolean column
         $table->boolean('is_active')->default(true);

         // Timestamps
         $table->timestamps();

         // Soft deletes
         $table->softDeletes();

         // Indexes
         $table->index('email');
      });
   }

   /**
    * Reverse the migration
    */
   public function down(): void
   {
      Schema::dropIfExists('users');
   }
}
