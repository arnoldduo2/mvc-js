<?php

declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= $title ?? 'MVC-JS' ?></title>
   <link rel="icon" type="image/svg+xml" href="<?= url('/favicon.svg') ?>">
   <link rel="stylesheet" href="<?= url('/css/app.css') ?>">
   <!-- Core SPA Scripts -->
   <?= useSpa() ?>
   <?= $head ?? '' ?>
</head>

<body>
   <!-- Loading indicator -->
   <div id="spa-loader"
      style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 3px; background: #007bff; z-index: 9999;">
   </div>

   <?php __includes('layouts.header'); ?>
   <!-- Main content container -->
   <div id="app">
      <?= $content ?? '' ?>
   </div>

   <?php __includes('layouts.footer'); ?>
</body>

</html>