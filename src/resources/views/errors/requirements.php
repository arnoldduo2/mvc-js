<?php

declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>System Requirements Failed - <?= APP_NAME ?></title>
   <style>
      body {
         font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
         background-color: #f3f4f6;
         color: #1f2937;
         display: flex;
         justify-content: center;
         align-items: center;
         min-height: 100vh;
         margin: 0;
         padding: 20px;
      }

      .container {
         background: white;
         border-radius: 12px;
         box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
         width: 100%;
         max-width: 800px;
         overflow: hidden;
      }

      .header {
         background-color: #ef4444;
         color: white;
         padding: 24px;
         text-align: center;
      }

      .header h1 {
         margin: 0;
         font-size: 1.5rem;
         font-weight: 600;
      }

      .header p {
         margin: 8px 0 0;
         opacity: 0.9;
      }

      .content {
         padding: 24px;
      }

      .section {
         margin-bottom: 24px;
      }

      .section-title {
         font-size: 1.1rem;
         font-weight: 600;
         margin-bottom: 12px;
         padding-bottom: 8px;
         border-bottom: 2px solid #e5e7eb;
         color: #374151;
      }

      .item {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 12px;
         border-bottom: 1px solid #f3f4f6;
      }

      .item:last-child {
         border-bottom: none;
      }

      .item-name {
         font-weight: 500;
      }

      .status {
         font-size: 0.875rem;
         font-weight: 600;
         padding: 4px 12px;
         border-radius: 9999px;
      }

      .status.pass {
         background-color: #d1fae5;
         color: #065f46;
      }

      .status.fail {
         background-color: #fee2e2;
         color: #991b1b;
      }

      .message {
         font-size: 0.875rem;
         color: #dc2626;
         margin-top: 4px;
         display: block;
         width: 100%;
      }

      .item-content {
         flex: 1;
      }
   </style>
</head>

<body>
   <div class="container">
      <div class="header">
         <h1>System Requirements Check Failed</h1>
         <p>Your server does not meet the minimum requirements to run this application.</p>
      </div>

      <div class="content">
         <!-- PHP Version -->
         <div class="section">
            <div class="section-title">Core</div>
            <div class="item">
               <div class="item-content">
                  <div class="item-name"><?= $results['php']['name'] ?></div>
                  <?php if (!$results['php']['pass']): ?>
                     <div class="message"><?= $results['php']['message'] ?></div>
                  <?php endif; ?>
               </div>
               <span class="status <?= $results['php']['pass'] ? 'pass' : 'fail' ?>">
                  <?= $results['php']['pass'] ? 'PASS' : 'FAIL' ?>
               </span>
            </div>
         </div>

         <!-- Extensions -->
         <?php if (!empty($results['extensions'])): ?>
            <div class="section">
               <div class="section-title">Extensions</div>
               <?php foreach ($results['extensions'] as $ext): ?>
                  <div class="item">
                     <div class="item-content">
                        <div class="item-name"><?= $ext['name'] ?></div>
                        <?php if (!$ext['pass']): ?>
                           <div class="message"><?= $ext['message'] ?></div>
                        <?php endif; ?>
                     </div>
                     <span class="status <?= $ext['pass'] ? 'pass' : 'fail' ?>">
                        <?= $ext['pass'] ? 'PASS' : 'FAIL' ?>
                     </span>
                  </div>
               <?php endforeach; ?>
            </div>
         <?php endif; ?>

         <!-- Environment -->
         <?php if (!empty($results['env'])): ?>
            <div class="section">
               <div class="section-title">Environment Variables</div>
               <?php foreach ($results['env'] as $env): ?>
                  <div class="item">
                     <div class="item-content">
                        <div class="item-name"><?= $env['name'] ?></div>
                        <?php if (!$env['pass']): ?>
                           <div class="message"><?= $env['message'] ?></div>
                        <?php endif; ?>
                     </div>
                     <span class="status <?= $env['pass'] ? 'pass' : 'fail' ?>">
                        <?= $env['pass'] ? 'PASS' : 'FAIL' ?>
                     </span>
                  </div>
               <?php endforeach; ?>
            </div>
         <?php endif; ?>

         <!-- Functions -->
         <?php if (!empty($results['functions'])): ?>
            <div class="section">
               <div class="section-title">Functions</div>
               <?php foreach ($results['functions'] as $func): ?>
                  <div class="item">
                     <div class="item-content">
                        <div class="item-name"><?= $func['name'] ?></div>
                        <?php if (!$func['pass']): ?>
                           <div class="message"><?= $func['message'] ?></div>
                        <?php endif; ?>
                     </div>
                     <span class="status <?= $func['pass'] ? 'pass' : 'fail' ?>">
                        <?= $func['pass'] ? 'PASS' : 'FAIL' ?>
                     </span>
                  </div>
               <?php endforeach; ?>
            </div>
         <?php endif; ?>
      </div>
   </div>
</body>

</html>