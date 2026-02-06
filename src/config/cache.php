<?php

declare(strict_types=1);

/**
 * Cache Configuration
 */

return [
   /*
    |--------------------------------------------------------------------------
    | Default Cache TTL
    |--------------------------------------------------------------------------
    |
    | Default time to live for cached items in seconds
    |
    */
   'default_ttl' => (int) env('CACHE_TTL', 3600),

   /*
    |--------------------------------------------------------------------------
    | Cache Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "file", "redis", "memcached"
    | Currently only file driver is implemented
    |
    */
   'driver' => env('CACHE_DRIVER', 'file'),

   /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | Directory where cache files are stored
    |
    */
   'path' => APP_CACHE,

   /*
    |--------------------------------------------------------------------------
    | Cache Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable caching globally
    |
    */
   'enabled' => env('CACHE_ENABLED', true),

   /*
    |--------------------------------------------------------------------------
    | Auto Cleanup
    |--------------------------------------------------------------------------
    |
    | Automatically cleanup expired cache entries
    | Probability: 1 in X requests will trigger cleanup
    |
    */
   'auto_cleanup' => [
      'enabled' => true,
      'probability' => 100, // 1 in 100 requests
   ],
];
