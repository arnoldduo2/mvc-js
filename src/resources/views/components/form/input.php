<?php

declare(strict_types=1);

/**
 * Form Input Component
 * 
 * Mimics legacy input component style.
 * 
 * @var string $name Input name
 * @var string|null $id Input ID (defaults to name)
 * @var mixed $value Input value
 * @var string $type Input type (text, email, password, etc.)
 * @var string $containerClass Class for the container div
 * @var string $className Class for the input element
 * @var string $label Label text
 * @var string $placeholder Placeholder text
 * @var bool $has_label Whether to show the label
 * @var bool $required Is required?
 * @var bool $disabled Is disabled?
 * @var bool $readonly Is readonly?
 * @var mixed $step Step attribute
 * @var mixed $min Min attribute
 * @var mixed $max Max attribute
 * @var array $attr Additional attributes
 * @var string|null $feedback Error feedback message
 */

// Set defaults
$name ??= '';
$id ??= $name;
$value ??= '';
$type ??= 'text';
$containerClass ??= 'form-group';
$className ??= '';
$label ??= ucfirst($name);
$placeholder ??= '';
$has_label ??= true;
$required ??= false;
$disabled ??= false;
$readonly ??= false;
$step ??= '';
$min ??= '';
$max ??= '';
$attr ??= [];
$feedback ??= \App\App\Core\Form::error($name);

// Attribute building logic (inline replacement for legacy helpers)
$buildAttributes = function (array $attributes): string {
   $html = [];
   foreach ($attributes as $key => $val) {
      if (is_bool($val)) {
         if ($val) $html[] = $key;
      } else {
         $html[] = $key . '="' . htmlspecialchars((string) $val) . '"';
      }
   }
   return implode(' ', $html);
};

// Required/Disabled/Readonly logic
$paramAttrs = [];
if ($required) $paramAttrs['required'] = true;
if ($disabled) $paramAttrs['disabled'] = true;
if ($readonly) $paramAttrs['readonly'] = true;

// Merge all attributes
$allAttrs = array_merge($paramAttrs, $attr);
$attrString = $buildAttributes($allAttrs);

// Feedback class
$isInvalid = !empty($feedback) || (function_exists('has_error') && has_error($name));
if ($isInvalid) {
   $className .= ' is-invalid error'; // Add both for compatibility
}

// Check for error if feedback is empty but error exists
if (empty($feedback) && function_exists('error')) {
   $errorMsg = error($name);
   if ($errorMsg) {
      $feedback = strip_tags($errorMsg); // Strip tags to avoid double wrapping if error() returns HTML
   }
}
?>

<div class="<?= $containerClass ?>">
   <?php if ($has_label): ?>
      <label for="<?= $id ?>" class="col-form-label <?= $required ? 'required' : '' ?>">
         <?= $label ?>
         <?php if ($required): ?><span class="required">*</span><?php endif; ?>
      </label>
   <?php endif; ?>

   <input
      type="<?= $type ?>"
      id="<?= $id ?>"
      name="<?= $name ?>"
      class="form-control input-box <?= $className ?>"
      value="<?= htmlspecialchars((string) $value) ?>"
      placeholder="<?= $placeholder ?>"
      <?= $step ? "step='{$step}'" : '' ?>
      <?= ($min !== '' && $type === 'number') ? "min='{$min}'" : '' ?>
      <?= ($max !== '' && $type === 'number') ? "max='{$max}'" : '' ?>
      <?= ($min !== '' && $type !== 'number') ? "active-minlength='{$min}'" : '' ?>
      <?= ($max !== '' && $type !== 'number') ? "active-maxlength='{$max}'" : '' ?>
      <?= $attrString ?>>

   <?php if ($feedback): ?>
      <div class="error-message invalid-feedback"><?= $feedback ?></div>
   <?php endif; ?>

   <?php if (isset($slot)): ?>
      <small class="form-text text-muted"><?= $slot ?></small>
   <?php endif; ?>
</div>