<?php

declare(strict_types=1);

namespace App\Helpers;

class StringHelper
{
   public static function toSnakeCase(string $value): string
   {
      return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
   }

   public static function toCamelCase(string $value): string
   {
      return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
   }

   public static function toKebabCase(string $value): string
   {
      return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $value));
   }

   public static function toStudlyCase(string $value): string
   {
      return ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
   }

   public static function toPascalCase(string $value): string
   {
      return ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
   }

   public static function toTitleCase(string $value): string
   {
      return ucwords(str_replace('_', ' ', $value));
   }

   public static function toSentenceCase(string $value): string
   {
      return ucfirst(str_replace('_', ' ', $value));
   }

   public static function toScreamingSnakeCase(string $value): string
   {
      return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
   }

   public static function toScreamingKebabCase(string $value): string
   {
      return strtoupper(preg_replace('/(?<!^)[A-Z]/', '-$0', $value));
   }

   public static function toScreamingStudlyCase(string $value): string
   {
      return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
   }

   public static function toScreamingPascalCase(string $value): string
   {
      return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
   }

   public static function toScreamingTitleCase(string $value): string
   {
      return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
   }

   public static function toScreamingSentenceCase(string $value): string
   {
      return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
   }
}
