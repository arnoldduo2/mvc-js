<?php

declare(strict_types=1);

namespace App\App\Core;

use Closure;

/**
 * Cache Class
 * 
 * File-based caching system with TTL support and cache tags
 */
class Cache
{
   private static ?Cache $instance = null;
   private string $cachePath;
   private array $tags = [];

   private function __construct()
   {
      $this->cachePath = APP_CACHE;
      $this->ensureCacheDirectory();
   }

   /**
    * Get singleton instance
    */
   public static function getInstance(): Cache
   {
      if (self::$instance === null) {
         self::$instance = new self();
      }
      return self::$instance;
   }

   /**
    * Ensure cache directory exists
    */
   private function ensureCacheDirectory(): void
   {
      if (!is_dir($this->cachePath)) {
         mkdir($this->cachePath, 0755, true);
      }
   }

   /**
    * Get cache file path
    */
   private function getCachePath(string $key): string
   {
      $hash = md5($key);
      $dir = $this->cachePath . '/' . substr($hash, 0, 2);

      if (!is_dir($dir)) {
         mkdir($dir, 0755, true);
      }

      return $dir . '/' . $hash . '.cache';
   }

   /**
    * Get value from cache
    * 
    * @param string $key Cache key
    * @param mixed $default Default value if not found
    * @return mixed
    */
   public static function get(string $key, mixed $default = null): mixed
   {
      return self::getInstance()->retrieve($key, $default);
   }

   /**
    * Retrieve value from cache
    */
   private function retrieve(string $key, mixed $default = null): mixed
   {
      $path = $this->getCachePath($key);

      if (!file_exists($path)) {
         return $default;
      }

      $data = unserialize(file_get_contents($path));

      // Check if expired
      if ($data['expires_at'] !== null && $data['expires_at'] < time()) {
         $this->delete($key);
         return $default;
      }

      return $data['value'];
   }

   /**
    * Store value in cache
    * 
    * @param string $key Cache key
    * @param mixed $value Value to cache
    * @param int $ttl Time to live in seconds (0 = forever)
    * @return bool
    */
   public static function put(string $key, mixed $value, int $ttl = 3600): bool
   {
      return self::getInstance()->store($key, $value, $ttl);
   }

   /**
    * Store value in cache
    */
   private function store(string $key, mixed $value, int $ttl = 3600): bool
   {
      $path = $this->getCachePath($key);

      $data = [
         'value' => $value,
         'expires_at' => $ttl > 0 ? time() + $ttl : null,
         'created_at' => time(),
         'tags' => $this->tags
      ];

      $result = file_put_contents($path, serialize($data)) !== false;

      // Reset tags after storing
      $this->tags = [];

      return $result;
   }

   /**
    * Get from cache or execute callback and cache result
    * 
    * @param string $key Cache key
    * @param int $ttl Time to live in seconds
    * @param Closure $callback Callback to execute if cache miss
    * @return mixed
    */
   public static function remember(string $key, int $ttl, Closure $callback): mixed
   {
      $instance = self::getInstance();

      $value = $instance->retrieve($key);

      if ($value !== null) {
         return $value;
      }

      $value = $callback();
      $instance->store($key, $value, $ttl);

      return $value;
   }

   /**
    * Check if key exists in cache
    * 
    * @param string $key Cache key
    * @return bool
    */
   public static function has(string $key): bool
   {
      $instance = self::getInstance();
      $path = $instance->getCachePath($key);

      if (!file_exists($path)) {
         return false;
      }

      $data = unserialize(file_get_contents($path));

      // Check if expired
      if ($data['expires_at'] !== null && $data['expires_at'] < time()) {
         $instance->delete($key);
         return false;
      }

      return true;
   }

   /**
    * Delete cached item
    * 
    * @param string $key Cache key
    * @return bool
    */
   public static function forget(string $key): bool
   {
      return self::getInstance()->delete($key);
   }

   /**
    * Delete cached item
    */
   private function delete(string $key): bool
   {
      $path = $this->getCachePath($key);

      if (file_exists($path)) {
         return unlink($path);
      }

      return false;
   }

   /**
    * Clear all cache
    * 
    * @return bool
    */
   public static function flush(): bool
   {
      return self::getInstance()->clearAll();
   }

   /**
    * Clear all cache files
    */
   private function clearAll(): bool
   {
      $this->deleteDirectory($this->cachePath);
      $this->ensureCacheDirectory();
      return true;
   }

   /**
    * Recursively delete directory
    */
   private function deleteDirectory(string $dir): void
   {
      if (!is_dir($dir)) {
         return;
      }

      $items = scandir($dir);

      foreach ($items as $item) {
         if ($item === '.' || $item === '..') {
            continue;
         }

         $path = $dir . '/' . $item;

         if (is_dir($path)) {
            $this->deleteDirectory($path);
         } else {
            unlink($path);
         }
      }

      if ($dir !== $this->cachePath) {
         rmdir($dir);
      }
   }

   /**
    * Set cache tags for next operation
    * 
    * @param array|string $tags Tag or array of tags
    * @return Cache
    */
   public static function tags(array|string $tags): Cache
   {
      $instance = self::getInstance();
      $instance->tags = is_array($tags) ? $tags : [$tags];
      return $instance;
   }

   /**
    * Flush cache by tags
    * 
    * @return bool
    */
   public function flushTags(): bool
   {
      if (empty($this->tags)) {
         return false;
      }

      $flushed = 0;
      $this->scanCacheFiles(function ($path, $data) use (&$flushed) {
         if (!empty($data['tags'])) {
            foreach ($this->tags as $tag) {
               if (in_array($tag, $data['tags'])) {
                  unlink($path);
                  $flushed++;
                  break;
               }
            }
         }
      });

      $this->tags = [];
      return $flushed > 0;
   }

   /**
    * Scan all cache files
    */
   private function scanCacheFiles(Closure $callback): void
   {
      $iterator = new \RecursiveIteratorIterator(
         new \RecursiveDirectoryIterator($this->cachePath)
      );

      foreach ($iterator as $file) {
         if ($file->isFile() && $file->getExtension() === 'cache') {
            $data = unserialize(file_get_contents($file->getPathname()));
            $callback($file->getPathname(), $data);
         }
      }
   }

   /**
    * Clean up expired cache entries
    * 
    * @return int Number of deleted entries
    */
   public static function cleanup(): int
   {
      $instance = self::getInstance();
      $deleted = 0;

      $instance->scanCacheFiles(function ($path, $data) use (&$deleted) {
         if ($data['expires_at'] !== null && $data['expires_at'] < time()) {
            unlink($path);
            $deleted++;
         }
      });

      return $deleted;
   }

   /**
    * Get cache statistics
    * 
    * @return array
    */
   public static function stats(): array
   {
      $instance = self::getInstance();
      $total = 0;
      $expired = 0;
      $size = 0;

      $instance->scanCacheFiles(function ($path, $data) use (&$total, &$expired, &$size) {
         $total++;
         $size += filesize($path);

         if ($data['expires_at'] !== null && $data['expires_at'] < time()) {
            $expired++;
         }
      });

      return [
         'total_entries' => $total,
         'expired_entries' => $expired,
         'active_entries' => $total - $expired,
         'total_size' => $size,
         'size_formatted' => self::formatBytes($size)
      ];
   }

   /**
    * Format bytes to human readable
    */
   private static function formatBytes(int $bytes): string
   {
      $units = ['B', 'KB', 'MB', 'GB'];
      $i = 0;

      while ($bytes >= 1024 && $i < count($units) - 1) {
         $bytes /= 1024;
         $i++;
      }

      return round($bytes, 2) . ' ' . $units[$i];
   }

   /**
    * Prevent cloning
    */
   private function __clone() {}

   /**
    * Prevent unserialization
    */
   public function __wakeup()
   {
      throw new \Exception("Cannot unserialize singleton");
   }
}
