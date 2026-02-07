<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\App\Core\Component;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{
   public function test_component_buffering(): void
   {
      // Start buffering a component
      Component::open('ui.card', ['title' => 'Test Title']);

      echo "Test Content";

      // End buffering and capture output
      // We need to mock the view rendering or just check logic.
      // Since Component::close() calls View::component(), and view rendering might involve file inclusion
      // which is hard to unit test in isolation without a real app structure,
      // we might test the buffering stack logic if possible, or integration test it.

      // However, Component class might just hold the stack.
      // Let's look at Component code again.
      // It stores data in a static stack.

      // Ideally we should mock the View facade/class.
      // But for this simple test, we'll assume the helper/View working if integration is okay,
      // OR we just test that it returns a string.

      // Actually, to unit test Component::close() which calls View::component(), 
      // we need the View class to work.
      // Let's assume the View::component returns the HTML string.

      // This is more of an integration test for the Component+View system.

      try {
         $output = Component::close();
         $this->assertIsString($output);
         $this->assertStringContainsString('Test Content', $output);
         $this->assertStringContainsString('Test Title', $output);
      } catch (\Exception $e) {
         // If View file not found, it might throw.
         // We should ensure the component view exists (ui.card does exist).
         $this->fail("Component rendering failed: " . $e->getMessage());
      }
   }
}
