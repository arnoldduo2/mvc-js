<?php

declare(strict_types=1);


/**
 * Die Dump, Dumps information about a variable
 * @param mixed $var Variable to be dumped.
 * @return void
 */
//#[NoReturn]
function dd(mixed ...$var): void
{
   echo '<pre style="color:gray">';
   print_r($var);
   echo '</pre>';
   die;
}
/**
 * Dump, Dumps information about a variable
 * @param mixed $var Variable to be dumped.
 * @return void
 */
function dump(mixed ...$var): void
{
   echo '<pre style="color:gray">';
   print_r($var);
   echo '</pre>';
}
/**
 * Die Dump, Dumps information about a variable
 * @param mixed $var Variable to be dumped.
 * @return void
 */
// #[NoReturn]
function vd(mixed ...$var): void
{
   echo '<pre style="color:gray">';
   var_dump($var);
   echo '</pre>';
   die;
}

/**
 * Die Dump, Dumps information about a variable
 * @param mixed $var Variable to be dumped.
 * @param string $color Color of the dumped text, Gray is default.
 * @return void
 */
function __prev(mixed $var, string $color = 'gray'): void
{
   echo "<pre style=\"color:$color\">";
   print_r($var);
   echo '</pre>';
}
