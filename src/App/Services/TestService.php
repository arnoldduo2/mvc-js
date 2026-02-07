<?php

namespace App\App\Services;

class TestService implements TestServiceContract
{
   public function getMessage(): string
   {
      return "Dependency Injection is working!";
   }
}
