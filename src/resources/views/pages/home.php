<?php

declare(strict_types=1); ?>
<div class="docs-layout">
   <!-- Sidebar Navigation -->
   <aside class="docs-sidebar">
      <div class="sidebar-header">
         <h2><?= APP_NAME ?></h2>
         <p class="version">v1.0.0</p>
      </div>

      <nav class="docs-nav">
         <div class="nav-section">
            <h3>Getting Started</h3>
            <ul>
               <li><a href="#introduction">Introduction</a></li>
               <li><a href="#features">Features</a></li>
               <li><a href="#installation">Installation</a></li>
               <li><a href="#quick-start">Quick Start</a></li>
            </ul>
         </div>

         <div class="nav-section">
            <h3>Core Concepts</h3>
            <ul>
               <li><a href="#routing">Routing</a></li>
               <li><a href="#models">Models</a></li>
               <li><a href="#migrations">Migrations</a></li>
               <li><a href="#controllers">Controllers</a></li>
               <li><a href="#controller-helpers">Controller Helpers</a></li>
               <li><a href="#views">Views</a></li>
               <li><a href="#components">Components</a></li>
               <li><a href="#spa">SPA System</a></li>
            </ul>
         </div>

         <div class="nav-section">
            <h3>Advanced</h3>
            <ul>
               <li><a href="#middleware">Middleware</a></li>
               <li><a href="#service-providers">Service Providers</a></li>
               <li><a href="#caching">Caching</a></li>
               <li><a href="#validation">Validation</a></li>
               <li><a href="#helpers">Helpers</a></li>
            </ul>
         </div>

         <div class="nav-section">
            <h3>Resources</h3>
            <ul>
               <li><a href="<?= url('/about') ?>">About</a></li>
               <li><a href="<?= url('/cache') ?>">Cache Demo</a></li>
               <li><a href="<?= url('/forms') ?>">Forms Demo</a></li>
               <li><a href="https://github.com/arnoldduo2/mvc-js" target="_blank">GitHub</a></li>
            </ul>
         </div>
      </nav>
   </aside>

   <!-- Main Content -->
   <main class="docs-content">
      <div class="docs-container">

         <!-- Introduction -->
         <section id="introduction" class="doc-section">
            <h1>Welcome to <?= APP_NAME ?></h1>
            <p class="lead">A modern PHP MVC framework with Single Page Application capabilities, Laravel-style routing, and clean architecture.</p>

            <div class="feature-badges">
               <span class="badge">PHP 8.0+</span>
               <span class="badge">MVC Pattern</span>
               <span class="badge">SPA Ready</span>
               <span class="badge">Laravel-Style</span>
            </div>
         </section>

         <!-- Features -->
         <section id="features" class="doc-section">
            <h2>🚀 Core Features</h2>
            <div class="features-grid">
               <div class="feature-card">
                  <div class="feature-icon">🛣️</div>
                  <h3>Modern Router</h3>
                  <p>Laravel-style fluent API with middleware support, route groups, and named routes</p>
               </div>
               <div class="feature-card">
                  <div class="feature-icon">💾</div>
                  <h3>Eloquent-Style Models</h3>
                  <p>Fluent query builder, mass assignment protection, and automatic timestamps</p>
               </div>
               <div class="feature-card">
                  <div class="feature-icon">⚡</div>
                  <h3>SPA Integration</h3>
                  <p>Seamless single-page application with ES6 modules and AJAX navigation</p>
               </div>
               <div class="feature-card">
                  <div class="feature-icon">🔒</div>
                  <h3>Security First</h3>
                  <p>CSRF protection, input validation, and password complexity rules</p>
               </div>
               <div class="feature-card">
                  <div class="feature-icon">💨</div>
                  <h3>Caching System</h3>
                  <p>File-based cache with TTL, tags, and query result caching</p>
               </div>
               <div class="feature-card">
                  <div class="feature-icon">✅</div>
                  <h3>Form Validation</h3>
                  <p>Comprehensive validation rules with custom error messages</p>
               </div>
            </div>
         </section>

         <!-- Installation -->
         <section id="installation" class="doc-section">
            <h2>📦 Installation</h2>
            <div class="code-block">
               <div class="code-header">
                  <span>Terminal</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-bash"># Clone the repository
git clone https://github.com/arnoldduo2/mvc-js.git
cd mvc-js

# Install dependencies
composer install

# Configure environment
cp .env.example .env

# Set up database
php -r "require 'src/App/Core/Database.php'; Database::initialize();"</code></pre>
            </div>
         </section>

         <!-- Quick Start -->
         <section id="quick-start" class="doc-section">
            <h2>⚡ Quick Start</h2>
            <p>Create your first route and controller in minutes:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>routes/web.php</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">Router::get('/hello', [HelloController::class, 'index']);</code></pre>
            </div>

            <div class="code-block">
               <div class="code-header">
                  <span>Controllers/HelloController.php</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">class HelloController extends Controller
{
    public function index(): void
    {
        View::page('pages/hello', [
            'message' => 'Hello, World!'
        ]);
    }
}</code></pre>
            </div>
         </section>

         <!-- Routing -->
         <section id="routing" class="doc-section">
            <h2>🛣️ Routing</h2>
            <p>Define routes with a Laravel-style fluent API:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>Basic Routes</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">// Basic routes
Router::get('/users', [UserController::class, 'index']);
Router::post('/users', [UserController::class, 'store']);

// Route parameters
Router::get('/users/{id}', [UserController::class, 'show'])
    ->where('id', '[0-9]+');

// Named routes
Router::get('/profile', [ProfileController::class, 'show'])
    ->name('profile');

// Route groups
Router::group(['prefix' => 'api', 'middleware' => 'auth'], function() {
    Router::get('/users', [UserController::class, 'index']);
    Router::post('/users', [UserController::class, 'store']);
});</code></pre>
            </div>
         </section>

         <!-- Models -->
         <section id="models" class="doc-section">
            <h2>💾 Models</h2>
            <p>Eloquent-style models with fluent query builder:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>Model Example</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">class User extends Model
{
    protected static string $table = 'users';
    protected static array $fillable = ['name', 'email'];
}

// Query examples
$users = User::all();
$user = User::find(1);

$activeUsers = User::query()
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();</code></pre>
            </div>
         </section>

         <!-- Migrations -->
         <section id="migrations" class="doc-section">
            <h2>🏗️ Migrations</h2>
            <p>Manage your database schema with the Schema builder and migration files.</p>

            <h3>✨ CLI Commands</h3>
            <div class="code-block">
               <div class="code-header">
                  <span>Terminal</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-bash"># Create a new migration
php console make:migration create_products_table

# Run pending migrations
php console migrate

# Rollback last batch
php console migrate:rollback

# Show migration status
php console migrate:status</code></pre>
            </div>

            <h3>📐 Schema Builder</h3>
            <p>Define your table structure with a fluent interface:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>Create Table</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">use App\App\Database\Schema;
use App\App\Database\Blueprint;

Schema::create('orders', function (Blueprint $table) {
    $table->id();                                   // Auto-increment primary key
    $table->bigInteger('user_id')->unsigned();      // Foreign key column
    $table->decimal('total', 10, 2);                // Decimal with precision
    $table->enum('status', ['pending', 'paid']);    // Enum column
    $table->boolean('is_shipped')->default(false);  // Boolean with default
    $table->timestamps();                           // created_at, updated_at
    
    // Foreign Key Constraint
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});</code></pre>
            </div>

            <p><strong>Available Column Types:</strong> <code>string</code>, <code>text</code>, <code>integer</code>, <code>boolean</code>, <code>decimal</code>, <code>date</code>, <code>dateTime</code>, <code>timestamp</code>, <code>json</code>, <code>enum</code>, <code>softDeletes</code>.</p>

            <h3>🔄 Reverse Engineering</h3>
            <p>Generate migrations from an existing database schema:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>Generate Migrations</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-bash"># Generate migrations for all tables
php console migrate:generate

# Sync database with migration files (detect changes)
php console migrate:sync --fix</code></pre>
            </div>
         </section>

         <!-- Controllers -->
         <section id="controllers" class="doc-section">
            <h2>🎮 Controllers</h2>
            <p>Clean controller structure with helper methods:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>Controller Example</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">class UserController extends Controller
{
    public function index(): void
    {
        $users = User::all();
        View::page('users/index', ['users' => $users]);
    }

    public function store(): void
    {
        $validator = validate($_POST, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->errors();
            redirect('/users/create');
        }

        User::create($validator->validated());
        redirect('/users');
    }
}</code></pre>
            </div>
         </section>

         <!-- Controller Helper Methods -->
         <section id="controller-helpers" class="doc-section">
            <h2>🎯 Controller Helper Methods</h2>
            <p>The base Controller class provides comprehensive helper methods for common tasks. <a href="<?= url('/') ?>/CONTROLLER.md" target="_blank">View full documentation</a></p>

            <div class="code-block">
               <div class="code-header">
                  <span>Request & Validation</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">class UserController extends Controller
{
    public function store(): void
    {
        // Verify CSRF and validate in one line
        $this->verifyCsrfOrFail();
        
        // Validate with auto-redirect on failure
        $validated = $this->validateOrFail([
            'name' => 'required|min:3',
            'email' => 'required|email'
        ]);
        
        // Get specific inputs
        $data = $this->only(['name', 'email', 'phone']);
        
        User::create($validated);
        
        // Redirect with flash message
        $this->redirectWith(url('/users'), 'User created!', 'success');
    }
}</code></pre>
            </div>

            <div class="code-block">
               <div class="code-header">
                  <span>File Uploads</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">public function uploadAvatar(): void
{
    if ($this->hasFile('avatar')) {
        // Validate file
        $errors = $this->validateFile('avatar', [
            'maxSize' => 2097152, // 2MB
            'allowedTypes' => ['image/jpeg', 'image/png']
        ]);
        
        if (empty($errors)) {
            // Move file
            $path = $this->moveFile('avatar', 'public/uploads/avatars');
            $this->success('Avatar uploaded!', ['path' => $path]);
        }
    }
}</code></pre>
            </div>

            <div class="code-block">
               <div class="code-header">
                  <span>Authentication & Authorization</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">public function dashboard(): void
{
    // Require authentication or redirect
    $this->requireAuth();
    
    // Get user data
    $userId = $this->userId();
    $user = $this->user();
    
    $this->view('dashboard', ['user' => $user]);
}

public function login(): void
{
    // Only allow guests
    $this->requireGuest();
    $this->view('auth/login');
}</code></pre>
            </div>

            <div class="code-block">
               <div class="code-header">
                  <span>Session & Flash Messages</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">// Set flash message
$this->flash('success', 'Profile updated!');

// Set session data
$this->setSession('cart', $cartData);

// Get session data
$cart = $this->session('cart', []);

// Redirect back
$this->back();</code></pre>
            </div>

            <div class="code-block">
               <div class="code-header">
                  <span>Pagination & Utilities</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">public function index(): void
{
    $total = User::count();
    $pagination = $this->paginate($total, 20);
    
    $users = User::limit($pagination['per_page'])
        ->offset($pagination['offset'])
        ->get();
    
    $this->view('users/index', [
        'users' => $users,
        'pagination' => $pagination
    ]);
}</code></pre>
            </div>

            <div class="info-box">
               <strong>📖 Available Helper Methods:</strong>
               <ul style="margin-top: 10px; line-height: 1.8;">
                  <li><strong>Request:</strong> <code>input()</code>, <code>all()</code>, <code>only()</code>, <code>except()</code>, <code>has()</code>, <code>filled()</code></li>
                  <li><strong>Response:</strong> <code>view()</code>, <code>json()</code>, <code>success()</code>, <code>error()</code></li>
                  <li><strong>Redirect:</strong> <code>redirect()</code>, <code>back()</code>, <code>redirectWith()</code></li>
                  <li><strong>Validation:</strong> <code>validate()</code>, <code>validateOrFail()</code>, <code>verifyCsrf()</code>, <code>verifyCsrfOrFail()</code></li>
                  <li><strong>Session:</strong> <code>session()</code>, <code>setSession()</code>, <code>hasSession()</code>, <code>forgetSession()</code></li>
                  <li><strong>Flash:</strong> <code>flash()</code>, <code>getFlash()</code>, <code>hasFlash()</code></li>
                  <li><strong>Files:</strong> <code>hasFile()</code>, <code>file()</code>, <code>moveFile()</code>, <code>validateFile()</code></li>
                  <li><strong>Auth:</strong> <code>isAuthenticated()</code>, <code>userId()</code>, <code>user()</code>, <code>requireAuth()</code>, <code>requireGuest()</code></li>
                  <li><strong>Utilities:</strong> <code>abort()</code>, <code>paginate()</code>, <code>isPost()</code>, <code>isGet()</code>, <code>method()</code></li>
               </ul>
            </div>
         </section>

         <!-- Views -->
         <section id="views" class="doc-section">
            <h2>👁️ Views</h2>
            <p>Simple PHP templates with layout support:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>View Example</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">// Render view with layout
View::page('pages/home', ['title' => 'Home']);

// Render view only
$html = View::render('partials/navbar', $data);

// JSON response
View::json(['success' => true, 'data' => $data]);

// Cached view
View::cached('pages/home', $data, 3600);</code></pre>
            </div>
         </section>

         <!-- Components -->
         <section id="components" class="doc-section">
            <h2>🧩 Components</h2>
            <p>Reusable UI components with dot notation support. <a href="<?= url('/') ?>/COMPONENTS.md" target="_blank">View full documentation</a></p>

            <div class="code-block">
               <div class="code-header">
                  <span>Usage</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">// Render component
<?= "<?=" ?> View::component('ui.card', ['title' => 'Hello']) ?>

// Dot notation
<?= "<?=" ?> View::component('forms.input', ['name' => 'email']) ?></code></pre>
            </div>
         </section>

         <!-- SPA -->
         <section id="spa" class="doc-section">
            <h2>⚡ SPA System</h2>
            <p>Built-in single-page application with smooth transitions. <a href="<?= url('/') ?>/SPA_GUIDE.md" target="_blank">View full documentation</a></p>

            <div class="code-block">
               <div class="code-header">
                  <span>SPA Navigation</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-javascript">// Automatic SPA navigation for all links
&lt;a href="/about"&gt;About&lt;/a&gt;

// Disable SPA for specific links
&lt;a href="/download" data-no-spa&gt;Download&lt;/a&gt;

// Programmatic navigation
import { navigateTo } from './app-router.js';
navigateTo('/dashboard');</code></pre>
            </div>
         </section>

         <!-- Middleware -->
         <section id="middleware" class="doc-section">
            <h2>🔐 Middleware</h2>
            <p>Protect routes with authentication and authorization:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>Middleware Example</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">// Single middleware
Router::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth');

// Multiple middleware
Router::get('/admin', [AdminController::class, 'index'])
    ->middleware(['auth', 'role:admin']);

// Group middleware
Router::group(['middleware' => 'auth'], function() {
    Router::get('/profile', [ProfileController::class, 'show']);
    Router::get('/settings', [SettingsController::class, 'index']);
});</code></pre>
            </div>
         </section>

         <!-- Service Providers -->
         <section id="service-providers" class="doc-section">
            <h2>🔌 Service Providers</h2>
            <p>Manage class dependencies and perform dependency injection. <a href="<?= url('/') ?>/SERVICE_PROVIDERS.md" target="_blank">View full documentation</a></p>

            <div class="code-block">
               <div class="code-header">
                  <span>Service Provider</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
    }
}</code></pre>
            </div>
         </section>

         <!-- Caching -->
         <section id="caching" class="doc-section">
            <h2>💨 Caching</h2>
            <p>Improve performance with file-based caching:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>Caching Examples</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">// Basic caching
Cache::put('users', $users, 3600);
$users = Cache::get('users');

// Cache remember
$users = cache_remember('users', 3600, function() {
    return User::all();
});

// Query caching
$users = User::query()
    ->where('status', 'active')
    ->cache(3600)
    ->get();

// Clear cache
User::flushCache();
Cache::flush();</code></pre>
            </div>
         </section>

         <!-- Validation -->
         <section id="validation" class="doc-section">
            <h2>✅ Validation</h2>
            <p>Comprehensive form validation with CSRF protection:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>Validation Example</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">// Validate form data
$validator = validate($_POST, [
    'name' => 'required|min:3|max:50',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|password:min=8,upper,lower,number,special|confirmed',
    'age' => 'required|numeric|min:18|max:120'
]);

if ($validator->fails()) {
    $_SESSION['errors'] = $validator->errors();
    redirect('/form');
}

$data = $validator->validated();</code></pre>
            </div>

            <div class="code-block">
               <div class="code-header">
                  <span>Form with CSRF</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">&lt;?= Form::open('/submit', 'POST') ?&gt;
    &lt;?= Form::input('name', old('name')) ?&gt;
    &lt;?= Form::error('name') ?&gt;
    
    &lt;?= Form::email('email', old('email')) ?&gt;
    &lt;?= Form::error('email') ?&gt;
    
    &lt;?= Form::submit('Submit') ?&gt;
&lt;?= Form::close() ?&gt;</code></pre>
            </div>
         </section>

         <!-- Helpers -->
         <section id="helpers" class="doc-section">
            <h2>🛠️ Helpers</h2>
            <p>Useful helper functions for common tasks:</p>

            <div class="code-block">
               <div class="code-header">
                  <span>Helper Functions</span>
                  <button class="copy-btn" onclick="copyCode(this)">Copy</button>
               </div>
               <pre><code class="language-php">// Environment
$debug = env('APP_DEBUG', false);

// URLs
$url = url('/dashboard');

// Caching
cache('key', 'value', 3600);
$value = cache('key');

// CSRF
csrf_token();
csrf_field();

// Validation
old('email');
error('email');
has_error('email');

// String helpers
snakeCase('HelloWorld');  // hello_world
camelCase('hello_world'); // helloWorld</code></pre>
            </div>
         </section>

         <!-- Footer -->
         <footer class="docs-footer">
            <p>Built with ❤️ by the MVC-JS Team</p>
            <p>
               <a href="https://github.com/arnoldduo2/mvc-js" target="_blank">GitHub</a> •
               <a href="<?= url('/about') ?>">About</a> •
               <a href="<?= url('/cache') ?>">Cache Demo</a> •
               <a href="<?= url('/forms') ?>">Forms Demo</a>
            </p>
         </footer>

      </div>
   </main>
</div>

<script>
   // Smooth scroll for anchor links
   document.querySelectorAll('.docs-nav a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
         e.preventDefault();
         const target = document.querySelector(this.getAttribute('href'));
         if (target) {
            target.scrollIntoView({
               behavior: 'smooth',
               block: 'start'
            });

            // Update active state
            document.querySelectorAll('.docs-nav a').forEach(a => a.classList.remove('active'));
            this.classList.add('active');
         }
      });
   });

   // Highlight active section on scroll
   const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
         if (entry.isIntersecting) {
            const id = entry.target.getAttribute('id');
            document.querySelectorAll('.docs-nav a').forEach(a => {
               a.classList.remove('active');
               if (a.getAttribute('href') === '#' + id) {
                  a.classList.add('active');
               }
            });
         }
      });
   }, {
      threshold: 0.5
   });

   document.querySelectorAll('.doc-section').forEach(section => {
      observer.observe(section);
   });

   // Copy code function
   function copyCode(button) {
      const codeBlock = button.closest('.code-block').querySelector('code');
      const text = codeBlock.textContent;

      navigator.clipboard.writeText(text).then(() => {
         button.textContent = 'Copied!';
         setTimeout(() => {
            button.textContent = 'Copy';
         }, 2000);
      });
   }

   console.log('📚 Documentation page loaded!');
</script>