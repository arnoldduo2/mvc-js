<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;
use App\App\Core\Validator;
use App\App\Core\CSRF;
use App\App\Models\User;

/**
 * User Controller
 */
class UserController extends Controller
{
   /**
    * Display a listing of users
    */
   public function index(): void
   {
      // Get page number from query string
      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      if ($page < 1) $page = 1;

      $paginator = User::paginate(10, $page);

      $this->view('users/index', [
         'title' => 'Users Management',
         'users' => $paginator['data'],
         'pagination' => $paginator
      ]);
   }

   /**
    * Show the form for creating a new user
    */
   public function create(): void
   {
      $this->view('users/create', [
         'title' => 'Create New User'
      ]);
   }

   /**
    * Store a newly created user in storage
    */
   public function store(): void
   {
      // Verify CSRF token
      if (!CSRF::verify($_POST)) {
         $_SESSION['errors'] = ['_token' => ['Invalid CSRF token. Please try again.']];
         redirect(url('/users/create'));
         return;
      }

      // Validate form data
      $validator = Validator::make($_POST, [
         'name' => 'required|min:3|max:100',
         'email' => 'required|email|unique:users,email',
         'password' => 'required|min:8|confirmed',
         'role' => 'required|in:admin,user,guest',
         'status' => 'in:active,inactive'
      ]);

      if ($validator->fails()) {
         $_SESSION['errors'] = $validator->errors();
         $_SESSION['old'] = $_POST;
         redirect(url('/users/create'));
         return;
      }

      // Create user
      $data = $validator->validated();
      $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
      $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;

      // Remove password_confirmation and status (mapped to is_active or role)
      unset($data['password_confirmation']);

      User::create($data);

      $_SESSION['success'] = 'User created successfully.';
      redirect(url('/users'));
   }

   /**
    * Display the specified user
    */
   public function show(int $id): void
   {
      $user = User::find($id);

      if (!$user) {
         http_response_code(404);
         $this->view('errors/404', ['title' => 'User Not Found']);
         return;
      }

      $this->view('users/show', [
         'title' => 'User Details',
         'user' => $user
      ]);
   }

   /**
    * Show the form for editing the specified user
    */
   public function edit(int $id): void
   {
      $user = User::find($id);

      if (!$user) {
         http_response_code(404);
         $this->view('errors/404', ['title' => 'User Not Found']);
         return;
      }

      $this->view('users/edit', [
         'title' => 'Edit User',
         'user' => $user
      ]);
   }

   /**
    * Update the specified user in storage
    */
   public function update(int $id): void
   {
      $user = User::find($id);

      if (!$user) {
         http_response_code(404);
         $this->view('errors/404', ['title' => 'User Not Found']);
         return;
      }

      // Verify CSRF token
      if (!CSRF::verify($_POST)) {
         $_SESSION['errors'] = ['_token' => ['Invalid CSRF token. Please try again.']];
         redirect(url("/users/{$id}/edit"));
         return;
      }

      // Validate form data
      $rules = [
         'name' => 'required|min:3|max:100',
         'email' => 'required|email',
         'role' => 'required|in:admin,user,guest'
      ];

      // Only validate password if provided
      if (!empty($_POST['password'])) {
         $rules['password'] = 'min:8|confirmed';
      }

      $validator = Validator::make($_POST, $rules);

      // Check uniqueness of email if changed
      if ($_POST['email'] !== $user['email'] && User::exists('email', $_POST['email'])) {
         $validator->addError('email', 'This email is already taken.');
      }

      if ($validator->fails()) {
         $_SESSION['errors'] = $validator->errors();
         $_SESSION['old'] = $_POST;
         redirect(url("/users/{$id}/edit"));
         return;
      }

      // Update user
      $data = $validator->validated();

      // Handle password update
      if (!empty($data['password'])) {
         $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
      } else {
         unset($data['password']);
      }

      unset($data['password_confirmation']);
      $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;

      User::update($id, $data);

      $_SESSION['success'] = 'User updated successfully.';
      redirect(url('/users'));
   }

   /**
    * Remove the specified user from storage
    */
   public function destroy(int $id): void
   {
      $user = User::find($id);

      if (!$user) {
         http_response_code(404);
         $this->view('errors/404', ['title' => 'User Not Found']);
         return;
      }

      // Verify CSRF token
      if (!CSRF::verify($_POST)) {
         $_SESSION['errors'] = ['_token' => ['Invalid CSRF token.']];
         redirect(url('/users'));
         return;
      }

      User::delete($id);

      $_SESSION['success'] = 'User deleted successfully.';
      redirect(url('/users'));
   }
}
