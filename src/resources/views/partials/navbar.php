<?php

declare(strict_types=1); ?>
<nav class="navbar">
   <div class="container">
      <a href="<?= url('/') ?>" class="logo"><?= APP_NAME ?></a>
      <ul class="nav-links">
         <li><a href="<?= url('/home') ?>">Home</a></li>
         <li><a href="<?= url('/about') ?>">About</a></li>
         <li><a href="<?= url('/dashboard') ?>">Dashboard</a></li>
         <li><a href="<?= url('/cache') ?>">Cache Demo</a></li>
         <li><a href="<?= url('/forms') ?>">Forms Demo</a></li>
         <li><a href="<?= url('/api/health') ?>" data-no-spa>API Health</a></li>
         <li><button class="dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode">🌙</button></li>
      </ul>
   </div>
</nav>