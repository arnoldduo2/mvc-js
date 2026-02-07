<?php

declare(strict_types=1); ?>
<div class="error-page">
   <div class="error-container">
      <div class="error-code">404</div>
      <h1 class="error-title">Page Not Found</h1>
      <p class="error-message">
         Oops! The page you're looking for doesn't exist. It might have been moved or deleted.
      </p>
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
   console.log('404 Error page loaded');
</script>