<?php

declare(strict_types=1);

/**
 * UI Card Component
 * 
 * @var string $slot Content inside the card
 * @var string|null $title Optional card title
 * @var string|null $footer Optional card footer
 * @var string|null $class Optional additional CSS classes
 */
?>
<div class="card <?= $class ?? '' ?>">
   <?php if (!empty($title)): ?>
      <div class="card-header">
         <h3 class="card-title"><?= htmlspecialchars($title) ?></h3>
      </div>
   <?php endif; ?>

   <div class="card-body">
      <?= $slot ?>
   </div>

   <?php if (!empty($footer)): ?>
      <div class="card-footer">
         <?= $footer ?>
      </div>
   <?php endif; ?>
</div>