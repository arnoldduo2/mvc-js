<?php

declare(strict_types=1);

namespace App\Helpers;

class AppLogicHelpers
{
   public static function init(string $app_name, string $app_path): void
   {
      $app_path = rtrim($app_path, "/") . "/";
      $app_name = strtolower($app_name);
   }
   public static function __getUser(string $key): mixed
   {
      return $_SESSION['user'][$key] ?? null;
   }
}
