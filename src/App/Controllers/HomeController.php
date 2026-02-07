<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;

class HomeController extends Controller
{
   public function index()
   {
      return view('home', [
         'title' => 'Welcome',
      ]);
   }
}
