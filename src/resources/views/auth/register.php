<?php use App\App\Core\Form; ?>

<div class="row justify-content-center">
   <div class="col-md-8">
      <div class="card">
         <div class="card-header">
            <h3 class="card-title mb-0">Create Account</h3>
         </div>
         <div class="card-body">
            <?php if (has_error('_token')): ?>
               <div class="alert alert-danger">
                  <?= error('_token') ?>
               </div>
            <?php endif; ?>

            <?= Form::open(url('/register'), 'POST', ['data-no-spa' => true]) ?>

            <div class="mb-3">
               <label for="name" class="form-label">Full Name</label>
               <?= Form::input('name', old('name'), [
                  'class' => 'form-control' . (has_error('name') ? ' is-invalid' : ''),
                  'required' => true,
                  'autofocus' => true
               ]) ?>
               <?= Form::error('name') ?>
            </div>

            <div class="mb-3">
               <label for="email" class="form-label">Email Address</label>
               <?= Form::email('email', old('email'), [
                  'class' => 'form-control' . (has_error('email') ? ' is-invalid' : ''),
                  'required' => true
               ]) ?>
               <?= Form::error('email') ?>
            </div>

            <div class="row">
               <div class="col-md-6 mb-3">
                  <label for="password" class="form-label">Password</label>
                  <?= Form::password('password', [
                     'class' => 'form-control' . (has_error('password') ? ' is-invalid' : ''),
                     'required' => true
                  ]) ?>
                  <?= Form::error('password') ?>
                  <small class="form-text text-muted">Minimum 8 characters</small>
               </div>
               <div class="col-md-6 mb-3">
                  <label for="password_confirmation" class="form-label">Confirm Password</label>
                  <?= Form::password('password_confirmation', [
                     'class' => 'form-control',
                     'required' => true
                  ]) ?>
               </div>
            </div>

            <div class="d-grid gap-2">
               <?= Form::submit('Create Account', ['class' => 'btn btn-primary']) ?>
            </div>

            <div class="mt-3 text-center">
               <p class="mb-0">Already have an account? <a href="<?= url('/login') ?>">Login here</a></p>
            </div>

            <?= Form::close() ?>
         </div>
      </div>
   </div>
</div>
