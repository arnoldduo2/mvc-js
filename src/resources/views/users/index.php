<div class="row mb-4">
   <div class="col-md-6">
      <h1>Users Management</h1>
   </div>
   <div class="col-md-6 text-end">
      <a href="<?= url('/users/create') ?>" class="btn btn-primary">
         <i class="fas fa-plus"></i> Add New User
      </a>
   </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
   <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $_SESSION['success'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
   <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="card">
   <div class="card-body">
      <div class="table-responsive">
         <table class="table table-hover">
            <thead>
               <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Actions</th>
               </tr>
            </thead>
            <tbody>
               <?php if (empty($users)): ?>
                  <tr>
                     <td colspan="6" class="text-center">No users found.</td>
                  </tr>
               <?php else: ?>
                  <?php foreach ($users as $user): ?>
                     <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                           <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'user' ? 'info' : 'secondary') ?>">
                              <?= ucfirst($user['role']) ?>
                           </span>
                        </td>
                        <td>
                           <span class="badge bg-<?= $user['is_active'] ? 'success' : 'warning' ?>">
                              <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                           </span>
                        </td>
                        <td>
                           <a href="<?= url('/users/' . $user['id']) ?>" class="btn btn-sm btn-info" title="View">
                              <i class="fas fa-eye"></i>
                           </a>
                           <a href="<?= url('/users/' . $user['id'] . '/edit') ?>" class="btn btn-sm btn-warning" title="Edit">
                              <i class="fas fa-edit"></i>
                           </a>
                           <form action="<?= url('/users/' . $user['id'] . '/delete') ?>" method="POST" class="d-inline" data-no-spa onsubmit="return confirm('Are you sure you want to delete this user?');">
                              <?= csrf_field() ?>
                              <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                 <i class="fas fa-trash"></i>
                              </button>
                           </form>
                        </td>
                     </tr>
                  <?php endforeach; ?>
               <?php endif; ?>
            </tbody>
         </table>
      </div>

      <!-- Pagination -->
      <?php if ($pagination['last_page'] > 1): ?>
         <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
               <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
                  <a class="page-link" href="<?= url('/users?page=' . ($pagination['current_page'] - 1)) ?>">Previous</a>
               </li>
               <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                  <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                     <a class="page-link" href="<?= url('/users?page=' . $i) ?>"><?= $i ?></a>
                  </li>
               <?php endfor; ?>
               <li class="page-item <?= $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' ?>">
                  <a class="page-link" href="<?= url('/users?page=' . ($pagination['current_page'] + 1)) ?>">Next</a>
               </li>
            </ul>
         </nav>
      <?php endif; ?>
   </div>
</div>