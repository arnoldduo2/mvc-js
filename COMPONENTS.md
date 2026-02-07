# View Components

Components allow you to create reusable pieces of UI that can be shared across your application.

## Creating Components

Components are stored in the `src/resources/views/components` directory. You can organize them into subdirectories as needed.

**Example Component:** `src/resources/views/components/ui/card.php`

```php
<div class="card">
    <div class="card-header">
        <?= $title ?>
    </div>
    <div class="card-body">
        <?= $slot ?? '' ?>
    </div>
</div>
```

## Using Components

To render a component, use the `View::component()` method. You can use dot notation to reference nested components.

```php
use App\App\Core\View;
```

**Basic Usage:**

```php
<?= View::component('ui.card', ['title' => 'My Component']) ?>
```

**Passing Data:**

You can pass an array of data as the second argument. The keys will be extracted as variables within the component.

```php
<?= View::component('profile.avatar', [
    'user' => $user,
    'size' => 'large'
]) ?>
```

## Global Helper

If you prefer, you can define a helper function for even cleaner syntax (optional user implementation):

```php
function component($name, $data = []) {
    return \App\App\Core\View::component($name, $data);
}
```

Then usages becomes:

```php
<?= component('ui.card', ['title' => 'Hello']) ?>
```
