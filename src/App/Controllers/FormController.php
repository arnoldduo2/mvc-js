<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;
use App\App\Core\View;
use App\App\Core\Validator;
use App\App\Core\CSRF;

/**
 * Form Validation Demo Controller
 */
class FormController extends Controller
{
   /**
    * Display form demo page
    */
   public function index(): void
   {
      $content = View::render('pages/form-demo', [
         'title' => 'Form Validation Demo'
      ]);

      if (
         isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
         $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
      ) {
         View::json([
            'type' => 'html',
            'content' => $content,
            'title' => 'Form Validation Demo'
         ]);
      } else {
         echo View::render('layouts/app', [
            'title' => 'Form Validation Demo',
            'content' => $content
         ]);
      }
   }

   /**
    * Handle form submission
    */
   public function submit(): void
   {
      // Verify CSRF token
      if (!CSRF::verify($_POST)) {
         $_SESSION['errors'] = ['_token' => ['Invalid CSRF token. Please try again.']];
         redirect(url('/forms'));
         return;
      }

      // Validate form data
      $validator = Validator::make($_POST, [
         'name' => 'required|min:3|max:50',
         'email' => 'required|email',
         'age' => 'required|numeric|min:18|max:120',
         'website' => 'url',
         'password' => 'required|password|confirmed',
         'gender' => 'required|in:male,female,other',
         'terms' => 'required'
      ]);

      if ($validator->fails()) {
         // Store errors and old input in session
         $_SESSION['errors'] = $validator->errors();
         $_SESSION['old'] = $_POST;
         redirect(url('/forms'));
         return;
      }

      // Validation passed
      $_SESSION['success'] = 'Form submitted successfully!';
      $_SESSION['validated_data'] = $validator->validated();
      redirect(url('/forms/success'));
   }

   /**
    * Success page
    */
   public function success(): void
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }

      $success = $_SESSION['success'] ?? null;
      $data = $_SESSION['validated_data'] ?? [];

      unset($_SESSION['success'], $_SESSION['validated_data']);

      $content = View::render('pages/form-success', [
         'title' => 'Form Submitted',
         'message' => $success,
         'data' => $data
      ]);

      if (
         isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
         $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
      ) {
         View::json([
            'type' => 'html',
            'content' => $content,
            'title' => 'Form Submitted'
         ]);
      } else {
         echo View::render('layouts/app', [
            'title' => 'Form Submitted',
            'content' => $content
         ]);
      }
   }
}
