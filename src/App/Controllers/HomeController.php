<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;

/**
 * Home Controller
 */
class HomeController extends Controller
{
   /**
    * Show home page
    */
   public function index(): void
   {
      $this->view('pages/home', [
         'title' => 'Home - ' . APP_NAME,
      ]);
   }

   /**
    * Show about page
    */
   public function about(): void
   {
      $this->view('pages/about', [
         'title' => 'About - ' . APP_NAME,
      ]);
   }

   /**
    * Show dashboard
    */
   public function dashboard(): void
   {
      $this->view('pages/dashboard', [
         'title' => 'Dashboard - ' . APP_NAME,
         'stats' => [
            'users' => 150,
            'sessions' => 42,
            'views' => 1250,
         ],
         'activities' => [
            'User John logged in',
            'New post created',
            'Settings updated',
            'Database backup completed',
         ],
      ]);
   }
}
