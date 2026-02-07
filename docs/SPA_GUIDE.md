# MVC-JS Single Page Application (SPA) Guide

This framework includes a robust, zero-configuration Single Page Application (SPA) system. It allows you to build dynamic, fast-loading applications without the complexity of a full frontend framework like React or Vue.

## Core Concepts

1.  **Server-First**: You write standard PHP controllers and views.
2.  **Client-Enhanced**: The `app-router.js` intercepts link clicks and form submissions, fetching the content via AJAX and updating the DOM seamlessly.
3.  **Lifecycle Management**: The framework automatically handles script scoping and timer cleanup to prevent memory leaks.

---

## 1. Creating Pages

### Controller

In your controller, use the global `view()` helper. This helper automatically detects if the request is an SPA navigation or a full page load and returns the appropriate response (JSON or HTML).

```php
public function index()
{
    // Data to pass to the view
    $data = [
        'title' => 'Dashboard',
        'user' => 'John Doe',
        'stats' => ['views' => 100, 'clicks' => 50]
    ];

    // Arguments: View File, Data
    return view('dashboard', $data);
}
```

### View

Create your view file in `src/resources/views/pages/dashboard.php`.
Data passed from the controller is extracted as native PHP variables.

```php
<!-- src/resources/views/pages/dashboard.php -->
<div class="dashboard">
    <h1>Hello, <?= $user ?></h1>

    <div class="stats">
        <p>Views: <?= $stats['views'] ?></p>
        <p>Clicks: <?= $stats['clicks'] ?></p>
    </div>
</div>
```

---

## 2. Managing JavaScript

The SPA router includes advanced features to manage the JavaScript lifecycle, solving common issues with "persistent" browser states.

### Standard Scripts

You can include `<script>` tags directly in your view files. The router will find and execute them automatically when the page loads.

**Automatic Scoping**:
To prevent variable collisions (e.g., "Identifier 'x' has already been declared" when navigating back to a page), the framework **automatically wraps** your inline scripts in a block scope `{ ... }`.

```html
<!-- Safe to use const/let! -->
<script>
  const chart = new Chart(...); // This won't crash on page reload
  console.log('Page loaded');
</script>
```

### Timer Management

Common SPA Problem: `setInterval` keeps running even after you navigate away, causing memory leaks and errors.
**Solution**: The framework **automatically tracks and clears** all `setInterval` and `setTimeout` calls when you navigate to a new page.

```javascript
/* dashboard.php */
<script>
    // This timer will be AUTOMATICALLY cleared when you leave the page
    setInterval(() => {
        console.log('Polling data...');
    }, 1000);
</script>
```

### Advanced Cleanup

If you attach event listeners to `window` or `document` (which persist across pages), you should clean them up using the `page:unload` event.

```javascript
<script>
    function onScroll() { ... }
    window.addEventListener('scroll', onScroll);

    // Register cleanup
    document.addEventListener('page:unload', () => {
        window.removeEventListener('scroll', onScroll);
    }, { once: true });
</script>
```

---

## 3. Automatic Asset Injection

The framework simplifies asset management by automatically injecting CSS and JS files that match your view structure.

**How it works:**
If you render a view named `dashboard` (which resolves to `pages/dashboard`), the framework automatically looks for:

1.  `src/resources/css/pages/dashboard.css`
2.  `src/resources/js/pages/dashboard.js`

If found, they are automatically injected:

- **CSS**: Injected into the `<head>` (full load) or appended to content (SPA load).
- **JS**: Always appended to the end of the content.

You do NOT need to manually add `<link>` or `<script>` tags.

---

## 4. Components

The framework supports reusable components via the `View::component` method or by including partials.

**Usage:**

```php
<?= \App\App\Core\View::component('ui.card', ['title' => 'Hello']) ?>
```

**Location:**
This looks for `src/resources/views/components/ui/card.php`.

**Data:**
Variables passed in the array are available in the component view.

---

## 5. Best Practices

### Layouts

Your main layout (e.g., `src/resources/views/app.php`) must include the core scripts helper using `useSpa()`. This ensures the Router, Timer Patch, and Configuration are loaded in the correct order.

```php
<head>
    <!-- ... css links ... -->

    <!-- REQUIRED: Loads Timer Patch, App Config, and SPA Engine -->
    <?= useSpa() ?>
</head>
```

### Links

- **Internal Links**: `<a href="/about">` are automatically intercepted and loaded via SPA.
- **External Links**: `<a href="https://google.com">` are ignored and handled normally by the browser.
- **Opt-Out**: Add `data-no-spa` to any link to force a full page reload: `<a href="/logout" data-no-spa>Logout</a>`.

### Forms

- **AJAX Submission**: Add `data-spa-form` to a `<form>` to have the router handle the submission via AJAX automatically.
- **Redirects**: If your controller redirects after a form post, the SPA router will follow the redirect seamlessly.

---

## 6. CSS

Styles should generally be global (in `src/resources/css/app.css`). However, you can include page-specific styles in your view, though standard `<link>` tags in the body might cause a "Flash of Unstyled Content" (FOUC).

**Recommendation**: Use utility classes (like Tailwind or Bootstrap) or ensure your page-specific CSS is scoped with a unique parent ID/class to avoid conflicts.

```html
<style>
  /* Scoped to this page */
  .dashboard-page .stat-card {
    background: blue;
  }
</style>

<div class="dashboard-page">...</div>
```
