<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;

class HomeController extends Controller
{
   public function index(): void
   {
      $this->view('home', [
         'title' => 'Welcome',
      ]);
   }
}
