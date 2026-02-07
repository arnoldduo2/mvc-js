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

         <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Authenticated menu items -->
            <li><a href="<?= url('/users') ?>">Users</a></li>
            <li style="display: flex; align-items: center; gap: 10px;">
               <span style="color: var(--text-color);">Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'User') ?></span>
               <form action="<?= url('/logout') ?>" method="POST" style="display:inline; margin: 0;">
                  <?= csrf_field() ?>
                  <button type="submit" style="background: none; border: none; color: var(--link-color); cursor: pointer; text-decoration: underline; padding: 0; font: inherit;">Logout</button>
               </form>
            </li>
         <?php else: ?>
            <!-- Guest menu items -->
            <li><a href="<?= url('/login') ?>">Login</a></li>
            <li><a href="<?= url('/register') ?>">Register</a></li>
         <?php endif; ?>

         <li><a href="<?= url('/api/health') ?>" data-no-spa>API Health</a></li>
         <li><button class="dark-mode-toggle" id="darkModeToggle" aria-label="Toggle dark mode">🌙</button></li>
      </ul>
   </div>
</nav>