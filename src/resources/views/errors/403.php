<?php

declare(strict_types=1); ?>
<div class="error-page">
   <div class="error-container">
      <div class="error-code">403</div>
      <h1 class="error-title">Access Forbidden</h1>
      <p class="error-message">
         You don't have permission to access this resource. Please contact your administrator if you believe this is an error.
      </p>

      <?php if (!empty($permissions) || !empty($roles)): ?>
         <div class="error-details">
            <?php if (!empty($permissions)): ?>
               <p class="error-requirement">
                  <strong>Required permissions:</strong>
                  <code><?= implode(', ', $permissions) ?></code>
               </p>
            <?php endif; ?>

            <?php if (!empty($roles)): ?>
               <p class="error-requirement">
                  <strong>Required roles:</strong>
                  <code><?= implode(', ', $roles) ?></code>
               </p>
            <?php endif; ?>
         </div>
      <?php endif; ?>

      <div class="error-actions">
         <a href="<?= url('/') ?>" class="btn btn-primary">
            <span>🏠</span> Go Home
         </a>
         <button onclick="window.history.back()" class="btn btn-secondary">
            <span>←</span> Go Back
         </button>
      </div>
   </div>
</div>

<script>
   console.log('403 Forbidden page loaded');
</script>