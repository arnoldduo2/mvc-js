<div class="row justify-content-center">
   <div class="col-md-8">
      <div class="card">
         <div class="card-header">
            <h3 class="card-title mb-0">Edit User: <?= htmlspecialchars($user['name']) ?></h3>
         </div>
         <div class="card-body">
            <?php if (isset($_SESSION['errors'])): ?>
               <div class="alert alert-danger">
                  <ul class="mb-0">
                     <?php foreach ($_SESSION['errors'] as $field => $messages): ?>
                        <?php foreach ($messages as $message): ?>
                           <li><?= $message ?></li>
                        <?php endforeach; ?>
                     <?php endforeach; ?>
                  </ul>
               </div>
               <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <form action="<?= url('/users/' . $user['id']) ?>" method="POST">
               <?= csrf_field() ?>

               <div class="mb-3">
                  <label for="name" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $user['name']) ?>" required>
               </div>

               <div class="mb-3">
                  <label for="email" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
               </div>

               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label for="password" class="form-label">Password (Leave blank to keep current)</label>
                     <input type="password" class="form-control" id="password" name="password">
                  </div>
                  <div class="col-md-6 mb-3">
                     <label for="password_confirmation" class="form-label">Confirm Password</label>
                     <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                  </div>
               </div>

               <div class="mb-3">
                  <label for="role" class="form-label">Role</label>
                  <select class="form-select" id="role" name="role">
                     <option value="user" <?= old('role', $user['role']) === 'user' ? 'selected' : '' ?>>User</option>
                     <option value="admin" <?= old('role', $user['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
                     <option value="guest" <?= old('role', $user['role']) === 'guest' ? 'selected' : '' ?>>Guest</option>
                  </select>
               </div>

               <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?= old('is_active', $user['is_active']) ? 'checked' : '' ?> value="1">
                  <label class="form-check-label" for="is_active">Active Account</label>
               </div>

               <div class="d-flex justify-content-between">
                  <a href="<?= url('/users') ?>" class="btn btn-secondary">Cancel</a>
                  <button type="submit" class="btn btn-primary">Update User</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>