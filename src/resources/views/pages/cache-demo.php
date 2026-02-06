<?php

declare(strict_types=1); ?>
<div class="page-cache-demo">
   <div class="container">
      <h1><?= $title ?? 'Cache Demo' ?></h1>
      <p class="lead">Test and explore the caching system</p>

      <!-- Cache Statistics -->
      <section class="cache-stats">
         <h2>📊 Cache Statistics</h2>
         <div class="stats-grid">
            <div class="stat-card">
               <h3>Total Entries</h3>
               <div class="stat-number"><?= $stats['total_entries'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
               <h3>Active Entries</h3>
               <div class="stat-number"><?= $stats['active_entries'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
               <h3>Expired Entries</h3>
               <div class="stat-number"><?= $stats['expired_entries'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
               <h3>Total Size</h3>
               <div class="stat-number" style="font-size: 1.5rem;"><?= $stats['size_formatted'] ?? '0 B' ?></div>
            </div>
         </div>
      </section>

      <!-- Cache Actions -->
      <section class="cache-actions">
         <h2>🎯 Test Cache Operations</h2>
         <div class="action-buttons">
            <button class="btn btn-primary" onclick="testCache()">Test Basic Cache</button>
            <button class="btn btn-secondary" onclick="getStats()">Refresh Stats</button>
            <button class="btn" onclick="cleanupCache()">Cleanup Expired</button>
            <button class="btn" style="background: #f44336;" onclick="clearCache()">Clear All Cache</button>
         </div>
         <div id="cache-result" class="cache-result"></div>
      </section>

      <!-- Code Examples -->
      <section class="cache-examples">
         <h2>📝 Usage Examples</h2>

         <div class="example-block">
            <h3>Basic Caching</h3>
            <div class="code-example">
               <div class="code-example-header">PHP</div>
               <div class="code-block-wrapper">
                  <button class="copy-code-btn">Copy</button>
                  <pre><code class="language-php">// Store in cache
Cache::put('users', $users, 3600); // 1 hour

// Retrieve from cache
$users = Cache::get('users');

// Check if exists
if (Cache::has('users')) {
    // ...
}

// Delete from cache
Cache::forget('users');</code></pre>
               </div>
            </div>
         </div>

         <div class="example-block">
            <h3>Cache Remember</h3>
            <div class="code-example">
               <div class="code-example-header">PHP</div>
               <div class="code-block-wrapper">
                  <button class="copy-code-btn">Copy</button>
                  <pre><code class="language-php">// Get from cache or execute callback
$users = Cache::remember('users', 3600, function() {
    return User::all();
});

// Using helper
$users = cache_remember('users', 3600, function() {
    return User::all();
});</code></pre>
               </div>
            </div>
         </div>

         <div class="example-block">
            <h3>Query Caching</h3>
            <div class="code-example">
               <div class="code-example-header">PHP</div>
               <div class="code-block-wrapper">
                  <button class="copy-code-btn">Copy</button>
                  <pre><code class="language-php">// Cache query results for 1 hour
$users = User::query()
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->cache(3600)
    ->get();

// Clear model cache
User::flushCache();</code></pre>
               </div>
            </div>
         </div>

         <div class="example-block">
            <h3>Cache Tags</h3>
            <div class="code-example">
               <div class="code-example-header">PHP</div>
               <div class="code-block-wrapper">
                  <button class="copy-code-btn">Copy</button>
                  <pre><code class="language-php">// Tag cache entries
Cache::tags(['users', 'posts'])->put('user_posts', $data, 3600);

// Flush by tag
Cache::tags(['users'])->flushTags();</code></pre>
               </div>
            </div>
         </div>
      </section>

      <p style="margin-top: 2rem;">
         <a href="<?= url('/') ?>" class="btn">Back to Home</a>
         <a href="<?= url('/dashboard') ?>" class="btn btn-secondary">Dashboard</a>
      </p>
   </div>
</div>

<script>
   async function testCache() {
      const resultDiv = document.getElementById('cache-result');
      resultDiv.innerHTML = '<p>Testing cache... (this may take a moment on first run)</p>';

      try {
         const response = await fetch('<?= url('/cache/test') ?>');
         const data = await response.json();

         resultDiv.innerHTML = `
         <div class="success-message">
            <h4>✅ Cache Test Result</h4>
            <p><strong>Duration:</strong> ${data.duration_ms}ms</p>
            <p><strong>From Cache:</strong> ${data.from_cache ? 'Yes' : 'No (first run)'}</p>
            <p><strong>Data:</strong> ${JSON.stringify(data.data, null, 2)}</p>
            <p class="hint">Run again to see cached result (should be much faster!)</p>
         </div>
      `;
      } catch (error) {
         resultDiv.innerHTML = `<div class="error-message">Error: ${error.message}</div>`;
      }
   }

   async function getStats() {
      try {
         const response = await fetch('<?= url('/cache/stats') ?>');
         const data = await response.json();

         if (data.success) {
            location.reload();
         }
      } catch (error) {
         alert('Error fetching stats: ' + error.message);
      }
   }

   async function cleanupCache() {
      const resultDiv = document.getElementById('cache-result');

      try {
         const response = await fetch('<?= url('/cache/cleanup') ?>');
         const data = await response.json();

         resultDiv.innerHTML = `
         <div class="success-message">
            <h4>✅ ${data.message}</h4>
         </div>
      `;

         setTimeout(() => location.reload(), 1000);
      } catch (error) {
         resultDiv.innerHTML = `<div class="error-message">Error: ${error.message}</div>`;
      }
   }

   async function clearCache() {
      if (!confirm('Are you sure you want to clear all cache?')) {
         return;
      }

      const resultDiv = document.getElementById('cache-result');

      try {
         const response = await fetch('<?= url('/cache/clear') ?>');
         const data = await response.json();

         resultDiv.innerHTML = `
         <div class="success-message">
            <h4>✅ ${data.message}</h4>
         </div>
      `;

         setTimeout(() => location.reload(), 1000);
      } catch (error) {
         resultDiv.innerHTML = `<div class="error-message">Error: ${error.message}</div>`;
      }
   }

   console.log('Cache demo page loaded!');
</script>

<style>
   .cache-stats {
      margin: 3rem 0;
   }

   .cache-actions {
      margin: 3rem 0;
   }

   .action-buttons {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      margin: 2rem 0;
   }

   .cache-result {
      margin-top: 2rem;
      padding: 1.5rem;
      border-radius: 8px;
      min-height: 50px;
   }

   .success-message {
      background: var(--bg-secondary);
      padding: 1.5rem;
      border-radius: 8px;
      border-left: 4px solid var(--accent-color);
   }

   .success-message h4 {
      margin-bottom: 1rem;
      color: var(--accent-color);
   }

   .success-message p {
      margin: 0.5rem 0;
      color: var(--text-secondary);
   }

   .success-message .hint {
      margin-top: 1rem;
      font-style: italic;
      font-size: 0.9rem;
   }

   .error-message {
      background: #f44336;
      color: white;
      padding: 1rem;
      border-radius: 8px;
   }

   .cache-examples {
      margin: 3rem 0;
   }
</style>