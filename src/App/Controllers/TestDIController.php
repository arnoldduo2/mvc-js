<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;
use App\App\Services\TestServiceContract;

class TestDIController extends Controller
{
   protected TestServiceContract $service;

   public function __construct(TestServiceContract $service)
   {
      $this->service = $service;
   }

   public function index()
   {
      echo $this->service->getMessage();
   }
}
