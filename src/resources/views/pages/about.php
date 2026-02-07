<?php

declare(strict_types=1); ?>
<div class="page-about">
   <div class="container">
      <h1><?= $title ?? 'About MVC-JS Framework' ?></h1>

      <p class="lead">A modern PHP MVC framework with Single Page Application capabilities, built with clean architecture and best practices.</p>

      <section class="about-section">
         <h2>🚀 Core Features</h2>
         <ul class="tech-stack">
            <li><strong>Laravel-Style Router</strong> - Fluent API with middleware pipeline, route groups, and named routes</li>
            <li><strong>Streamlined Model</strong> - Query builder with mass assignment protection, automatic timestamps, and soft deletes</li>
            <li><strong>SPA System</strong> - Server-side rendering with client-side navigation using ES6 modules</li>
            <li><strong>Error Handler</strong> - Comprehensive error handling with AJAX/SPA detection and JSON responses</li>
            <li><strong>Modern PHP 8+</strong> - Type hints, strict types, and PSR-4 autoloading</li>
            <li><strong>Base Path Support</strong> - Works seamlessly in subdirectories and production</li>
            <li><strong>Dark Mode</strong> - Built-in dark mode with localStorage persistence</li>
         </ul>
      </section>

      <section class="about-section">
         <h2>🏗️ Architecture</h2>
         <p>Built on solid design principles:</p>
         <ul class="tech-stack">
            <li><strong>MVC Pattern</strong> - Clean separation of concerns</li>
            <li><strong>Singleton Pattern</strong> - Application and Database connections</li>
            <li><strong>Facade Pattern</strong> - Router and View classes</li>
            <li><strong>Builder Pattern</strong> - Query builder and route builder</li>
            <li><strong>Chain of Responsibility</strong> - Middleware pipeline</li>
            <li><strong>Active Record</strong> - Model layer with fluent interface</li>
         </ul>
      </section>

      <section class="about-section">
         <h2>⚡ How SPA Works</h2>
         <ol>
            <li><strong>Link Click</strong> → JavaScript intercepts the navigation</li>
            <li><strong>AJAX Request</strong> → Fetch API calls PHP endpoint with <code>X-Requested-With</code> header</li>
            <li><strong>Server Renders</strong> → PHP renders view with data</li>
            <li><strong>JSON Response</strong> → Returns JSON with HTML content, title, and scripts</li>
            <li><strong>DOM Update</strong> → JavaScript updates page content without reload</li>
            <li><strong>History API</strong> → Browser back/forward buttons work seamlessly</li>
         </ol>
      </section>

      <section class="about-section">
         <h2>🛠️ Technology Stack</h2>
         <div class="features">
            <div class="feature-card">
               <h3>Backend</h3>
               <ul>
                  <li>PHP 8.0+</li>
                  <li>PDO for database</li>
                  <li>Composer autoloading</li>
                  <li>PSR-4 standards</li>
               </ul>
            </div>
            <div class="feature-card">
               <h3>Frontend</h3>
               <ul>
                  <li>ES6 Modules</li>
                  <li>Vanilla JavaScript</li>
                  <li>CSS Variables</li>
                  <li>Fetch API</li>
               </ul>
            </div>
            <div class="feature-card">
               <h3>Tools</h3>
               <ul>
                  <li>Composer</li>
                  <li>Git</li>
                  <li>Apache/Nginx</li>
                  <li>MySQL/MariaDB</li>
               </ul>
            </div>
         </div>
      </section>

      <section class="about-section">
         <h2>👨‍💻 Author</h2>
         <div class="author-card">
            <h3>Arnold Tinashe Samhungu</h3>
            <p>Creator & Lead Developer</p>
            <p>
               <a href="https://github.com/arnoldduo2" target="_blank" rel="noopener">GitHub</a> |
               <a href="mailto:arnoldduo2@gmail.com">Email</a>
            </p>
         </div>
      </section>

      <section class="about-section">
         <h2>📚 Documentation</h2>
         <p>Comprehensive documentation available:</p>
         <ul class="tech-stack">
            <li><a href="https://github.com/arnoldduo2/mvc-js/blob/main/README.md" target="_blank">README</a> - Quick start and overview</li>
            <li><a href="https://github.com/arnoldduo2/mvc-js/blob/main/ARCHITECTURE.md" target="_blank">Architecture Guide</a> - System design and patterns</li>
            <li><a href="https://github.com/arnoldduo2/mvc-js/blob/main/AUTHORS.md" target="_blank">Authors</a> - Contributors and credits</li>
         </ul>
      </section>

      <section class="about-section">
         <h2>📄 License</h2>
         <p>This project is licensed under the MIT License - free to use, modify, and distribute.</p>
      </section>

      <p style="margin-top: 2rem;">
         <a href="<?= url('/') ?>" class="btn">Back to Home</a>
         <a href="<?= url('/dashboard') ?>" class="btn btn-secondary">View Dashboard</a>
      </p>
   </div>
</div>

<script>
   console.log('About page loaded via SPA!');
   console.log('Framework version: 1.0.0');
</script>