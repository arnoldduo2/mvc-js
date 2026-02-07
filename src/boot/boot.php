<?php

declare(strict_types=1);

/**
 * Router for PHP Built-in Server
 * 
 * This script is used by the PHP built-in server to handle requests.
 * It serves static files directly and routes everything else to index.php.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Handle masked resources (CSS/JS)
if (strpos($uri, '/css/') === 0 || strpos($uri, '/js/') === 0) {
   $resourcePath = __DIR__ . '/../../src/resources' . $uri;
   if (file_exists($resourcePath)) {
      $ext = pathinfo($resourcePath, PATHINFO_EXTENSION);
      $mimeTypes = [
         'css' => 'text/css',
         'js'  => 'application/javascript',
      ];
      if (isset($mimeTypes[$ext])) {
         header('Content-Type: ' . $mimeTypes[$ext]);
      } else {
         header('Content-Type: ' . mime_content_type($resourcePath));
      }
      readfile($resourcePath);
      exit;
   }
}

// Handle core JS resources
if (strpos($uri, '/core-js/') === 0) {
   // Remove /core-js/ prefix to get the file path
   $file = substr($uri, 9); // length of '/core-js/'
   $resourcePath = __DIR__ . '/../App/Core/resources/js/' . $file;

   if (file_exists($resourcePath)) {
      header('Content-Type: application/javascript');
      readfile($resourcePath);
      exit;
   }
}

// Provide your own way to serve other static resources
if ($uri !== '/' && file_exists(__DIR__ . '/../../' . $uri)) {
   return false;
}

require_once __DIR__ . '/../../index.php';
