<?php

declare(strict_types=1); ?>
<div class="test-card" style="border: 1px solid #ccc; padding: 1rem; margin: 1rem 0;">
   <h3><?= $title ?? 'Default Title' ?></h3>
   <div class="content">
      <?= $slot ?? '' ?>
   </div>
</div>