<div class="row justify-content-center">
   <div class="col-md-8">
      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">User Details</h3>
            <div>
               <a href="<?= url('/users/' . $user['id'] . '/edit') ?>" class="btn btn-warning btn-sm">
                  <i class="fas fa-edit"></i> Edit
               </a>
               <a href="<?= url('/users') ?>" class="btn btn-secondary btn-sm">
                  <i class="fas fa-arrow-left"></i> Back
               </a>
            </div>
         </div>
         <div class="card-body">
            <div class="row mb-3">
               <div class="col-md-4 fw-bold">ID:</div>
               <div class="col-md-8"><?= $user['id'] ?></div>
            </div>
            <div class="row mb-3">
               <div class="col-md-4 fw-bold">Name:</div>
               <div class="col-md-8"><?= htmlspecialchars($user['name']) ?></div>
            </div>
            <div class="row mb-3">
               <div class="col-md-4 fw-bold">Email:</div>
               <div class="col-md-8"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            <div class="row mb-3">
               <div class="col-md-4 fw-bold">Role:</div>
               <div class="col-md-8">
                  <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'user' ? 'info' : 'secondary') ?>">
                     <?= ucfirst($user['role']) ?>
                  </span>
               </div>
            </div>
            <div class="row mb-3">
               <div class="col-md-4 fw-bold">Status:</div>
               <div class="col-md-8">
                  <span class="badge bg-<?= $user['is_active'] ? 'success' : 'warning' ?>">
                     <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                  </span>
               </div>
            </div>
            <div class="row mb-3">
               <div class="col-md-4 fw-bold">Created At:</div>
               <div class="col-md-8"><?= $user['created_at'] ?></div>
            </div>
            <div class="row mb-3">
               <div class="col-md-4 fw-bold">Updated At:</div>
               <div class="col-md-8"><?= $user['updated_at'] ?></div>
            </div>
         </div>
      </div>
   </div>
</div>