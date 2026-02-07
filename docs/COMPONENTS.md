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

To render a component, use the `View::component()` method or the `component()` helper function. You can use dot notation to reference nested components.

**Basic Usage:**

```php
<?= component('ui.card', ['title' => 'My Component']) ?>
```

### Slot-Based Components

For components that wrap content (like a card or modal), use `component_open()` and `component_close()`. The content between these calls will be passed as the `$slot` variable.

```php
<?php component_open('ui.card', ['title' => 'My Card']) ?>
    <p>This is the content of the card.</p>
    <button>Click Me</button>
<?= component_close() ?>
```

### Legacy-Style Components

For components that mimic the legacy application style (e.g., input fields with labels and error handling), use the `form_component()` helper.

**Example: Legacy Input**

This renders a full form group with label, required asterisk, input field, and error feedback.

```php
<?= form_component('form.input', [
    'name' => 'username',
    'label' => 'Username',
    'value' => old('username'),
    'required' => true,
    'placeholder' => 'Enter username',
    'slot' => 'Unique username for your account' // Renders as help text
]) ?>
```

## Helper Functions

- `component(string $name, array $data = [])`: Render a component immediately.
- `component_open(string $name, array $data = [])`: Start a component buffering block.
- `component_close()`: End a component buffering block and render.
- `form_component(string $name, array $data = [])`: Render a form component (alias for `component` but often used for form logic).
