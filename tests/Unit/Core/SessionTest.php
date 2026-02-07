<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\App\Core\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
   protected function setUp(): void
   {
      // Start session if not started, or mock $_SESSION
      if (session_status() === PHP_SESSION_NONE) {
         // We can't easily start session in PHPUnit stdout sometimes due to headers.
         // But Session class might check status.
         // Let's just mock $_SESSION array directly as the Session class uses it.
         $_SESSION = [];
      }
   }

   public function test_put_and_get(): void
   {
      Session::put('key', 'value');
      $this->assertEquals('value', $_SESSION['key']);
      $this->assertEquals('value', Session::get('key'));
   }

   public function test_get_default(): void
   {
      $this->assertEquals('default', Session::get('non_existent', 'default'));
   }

   public function test_has(): void
   {
      Session::put('exists', 'true');
      $this->assertTrue(Session::has('exists'));
      $this->assertFalse(Session::has('non_existent'));
   }

   public function test_forget(): void
   {
      Session::put('key', 'value');
      Session::forget('key');

      $this->assertFalse(Session::has('key'));
      $this->assertArrayNotHasKey('key', $_SESSION);
   }

   public function test_flush(): void
   {
      Session::put('key1', 'value1');
      Session::put('key2', 'value2');

      Session::flush();

      $this->assertEmpty($_SESSION);
   }

   public function test_helper(): void
   {
      // Test helper function
      // Ensure helper file is loaded. Composer autoload should handle it if configured,
      // but we might need to verify 'src/Helpers/helpers.php' is loaded in bootstrap or test execution.
      // For now, we assume it's loaded via composer autoloader in bootstrap.

      session()->put('helper_key', 'helper_value');
      $this->assertEquals('helper_value', session('helper_key'));
   }
}
