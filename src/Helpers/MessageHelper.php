<?php

declare(strict_types=1);

class MessageHelper
{
   public static function sendAlert(string $type, string $message, bool $isHtml = true): string
   {
      $icon = match ($type) {
         'success' => '✅',
         'error' => '❌',
         'warning' => '⚠️',
         'info' => 'ℹ️',
         default => '💬',
      };

      $alertClass = match ($type) {
         'success' => 'alert-success',
         'error' => 'alert-danger',
         'warning' => 'alert-warning',
         'info' => 'alert-info',
         default => 'alert-secondary',
      };

      $html = "<div class='alert $alertClass' role='alert'>
                    <div class='d-flex align-items-center'>
                        <div class='flex-shrink-0'>
                            <span class='fs-4'>$icon</span>
                        </div>
                        <div class='flex-grow-1 ms-3'>
                            <strong>" . ucfirst($type) . ":</strong> " . $message . "
                        </div>
                    </div>
                </div>";

      return $isHtml ?
         json_encode(['type' => $type, 'message' => $html]) :
         json_encode(['type' => $type, 'message' => "$icon " . strip_tags($message)]);
   }

   /**
    * Get exception message. You can also pass an array of exceptions.
    * @param mixed $e
    * @return string
    */
   public static function eMsg($e): string
   {
      $msg = null;

      if (is_array($e)) {
         $e = (env('app_debug')) ? "Error {$e['type']}: {$e['message']} in file {$e['file']} on line {$e['line']}" : $e['message'];
      }
      $msg = 'Exception Server Error: Something didn\'t go right. Try again later or contact support.';
      if (str_contains($e, '1062 Duplicate entry')) $msg = '1062 Duplicate entry for documents is not allowed!';
      if (env('app_env') == 'development')
         $msg = "Exception Server Error: $e";
      return $msg;
   }
}