<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;
use App\App\Core\View;
use App\App\Core\Cache;

/**
 * Cache Demo Controller
 * 
 * Demonstrates caching functionality
 */
class CacheController extends Controller
{
   /**
    * Display cache demo page
    */
   public function index(): void
   {
      $stats = Cache::stats();

      // Render the view content
      $content = View::render('pages/cache-demo', [
         'title' => 'Cache Demo',
         'stats' => $stats
      ]);

      // Check if AJAX request
      if (
         isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
         $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
      ) {
         // Return JSON for SPA
         View::json([
            'type' => 'html',
            'content' => $content,
            'title' => 'Cache Demo'
         ]);
      } else {
         // Return full HTML page
         echo View::render('layouts/app', [
            'title' => 'Cache Demo',
            'content' => $content
         ]);
      }
   }

   /**
    * Test basic caching
    */
   public function testBasic(): void
   {
      $start = microtime(true);

      // Test cache remember
      $data = cache_remember('test_data', 60, function () {
         // Simulate expensive operation
         sleep(1);
         return [
            'message' => 'This data was generated at ' . date('Y-m-d H:i:s'),
            'random' => rand(1000, 9999)
         ];
      });

      $duration = round((microtime(true) - $start) * 1000, 2);

      View::json([
         'success' => true,
         'data' => $data,
         'duration_ms' => $duration,
         'from_cache' => $duration < 100 // If less than 100ms, likely from cache
      ]);
   }

   /**
    * Clear cache
    */
   public function clear(): void
   {
      Cache::flush();

      View::json([
         'success' => true,
         'message' => 'Cache cleared successfully'
      ]);
   }

   /**
    * Get cache statistics
    */
   public function stats(): void
   {
      $stats = Cache::stats();

      View::json([
         'success' => true,
         'stats' => $stats
      ]);
   }

   /**
    * Cleanup expired cache
    */
   public function cleanup(): void
   {
      $deleted = Cache::cleanup();

      View::json([
         'success' => true,
         'deleted' => $deleted,
         'message' => "Cleaned up {$deleted} expired cache entries"
      ]);
   }
}