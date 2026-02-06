<?php

declare(strict_types=1); ?>
<div class="page-dashboard">
   <div class="container">
      <h1><?= $title ?? 'Dashboard' ?></h1>

      <div class="stats-grid">
         <div class="stat-card">
            <h3>Total Users</h3>
            <p class="stat-number"><?= $stats['users'] ?? 0 ?></p>
         </div>
         <div class="stat-card">
            <h3>Active Sessions</h3>
            <p class="stat-number"><?= $stats['sessions'] ?? 0 ?></p>
         </div>
         <div class="stat-card">
            <h3>Page Views</h3>
            <p class="stat-number"><?= $stats['views'] ?? 0 ?></p>
         </div>
      </div>

      <div class="recent-activity">
         <h2>Recent Activity</h2>
         <ul>
            <?php foreach ($activities ?? [] as $activity): ?>
               <li><?= htmlspecialchars($activity) ?></li>
            <?php endforeach; ?>
         </ul>
      </div>
   </div>
</div>

<script>
   console.log('Dashboard loaded!');

   // Example: Update stats every 5 seconds
   setInterval(() => {
      console.log('Refreshing stats...');
   }, 5000);
</script>