<?php use App\App\Core\Form; ?>

<div class="row justify-content-center">
   <div class="col-md-6">
      <div class="card">
         <div class="card-header">
            <h3 class="card-title mb-0">Login</h3>
         </div>
         <div class="card-body">
            <?php if (has_error('_token')): ?>
               <div class="alert alert-danger">
                  <?= error('_token') ?>
               </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
               <div class="alert alert-success">
                  <?= $_SESSION['success'] ?>
               </div>
               <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?= Form::open(url('/login'), 'POST', ['data-no-spa' => true]) ?>

            <?php
            $returnUrl = $_GET['return'] ?? '';
            if ($returnUrl): ?>
               <input type="hidden" name="return_url" value="<?= htmlspecialchars($returnUrl) ?>">
            <?php endif; ?>

            <div class="mb-3">
               <label for="email" class="form-label">Email Address</label>
               <?= Form::email('email', old('email'), [
                  'class' => 'form-control' . (has_error('email') ? ' is-invalid' : ''),
                  'required' => true,
                  'autofocus' => true
               ]) ?>
               <?= Form::error('email') ?>
            </div>

            <div class="mb-3">
               <label for="password" class="form-label">Password</label>
               <?= Form::password('password', [
                  'class' => 'form-control' . (has_error('password') ? ' is-invalid' : ''),
                  'required' => true
               ]) ?>
               <?= Form::error('password') ?>
            </div>

            <div class="d-grid gap-2">
               <?= Form::submit('Login', ['class' => 'btn btn-primary']) ?>
            </div>

            <div class="mt-3 text-center">
               <p class="mb-0">Don't have an account? <a href="<?= url('/register') ?>">Register here</a></p>
            </div>

            <?= Form::close() ?>
         </div>
      </div>
   </div>
</div>
