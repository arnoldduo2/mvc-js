<?php

declare(strict_types=1);

namespace App\Helpers;

use Bcrypt\Bcrypt;

class EncyptionHelper
{
   private $bcrypt;
   public function __construct()
   {
      $this->bcrypt = new Bcrypt();
   }

   public function hash(string $value): string
   {
      return $this->bcrypt->hash($value);
   }

   public function verify(string $value, string $hash): bool
   {
      return $this->bcrypt->verify($value, $hash);
   }
}
