<?php

declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= $title ?? APP_NAME ?></title>
   <link rel="icon" type="image/svg+xml" href="<?= url('/favicon.svg') ?>">
   <link rel="stylesheet" href="<?= url('/css/app.css') ?>">
   <link rel="stylesheet" href="<?= url('/demo/assets/prism.css') ?>">
   <link rel="stylesheet" href="<?= url('/demo/assets/landing.css') ?>">
   <link rel="stylesheet" href="<?= url('/demo/assets/docs.css') ?>">
   <script>
      // Make base path available to JavaScript
      window.APP_BASE_PATH = '<?= \App\App\Core\Router::basePath() ?>';
   </script>
</head>

<body>
   <!-- Loading indicator -->
   <div id="spa-loader" style="display: none;">
      <div class="loader">Loading...</div>
   </div>

   <!-- Navigation -->
   <?php include __DIR__ . '/../partials/navbar.php'; ?>

   <!-- Main content container -->
   <div id="app">
      <?= $content ?? '' ?>
   </div>

   <!-- Syntax Highlighting (Demo - can be removed) -->
   <script src="<?= url('/demo/assets/prism.js') ?>"></script>

   <!-- SPA Application (ES6 Module) -->
   <script type="module" src="<?= url('/js/app.js') ?>"></script>
</body>

</html>