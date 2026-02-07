<?php

declare(strict_types=1);


?>

<div class="page-form-demo">
   <div class="container">
      <h1><?= $title ?? 'Form Validation Demo' ?></h1>
      <p class="lead">Test comprehensive form validation with CSRF protection</p>

      <!-- Validation Features -->
      <section class="features-section">
         <h2>✨ Validation Features</h2>
         <div class="features-grid">
            <div class="feature-item">✅ CSRF Protection</div>
            <div class="feature-item">✅ Required Fields</div>
            <div class="feature-item">✅ Email Validation</div>
            <div class="feature-item">✅ Min/Max Length</div>
            <div class="feature-item">✅ Numeric Validation</div>
            <div class="feature-item">✅ URL Validation</div>
            <div class="feature-item">✅ Password Complexity</div>
            <div class="feature-item">✅ Confirmation Fields</div>
            <div class="feature-item">✅ In Array Validation</div>
            <div class="feature-item">✅ Error Messages</div>
            <div class="feature-item">✅ Old Input Retention</div>
            <div class="feature-item">✅ Custom Rules</div>
         </div>
      </section>

      <!-- Demo Form -->
      <section class="form-section">
         <?php component_open('ui.card', ['title' => '📝 Demo Registration Form', 'class' => 'form-card']) ?>

         <?php if (has_error('_token')): ?>
            <div class="alert alert-error">
               <?= error('_token') ?>
            </div>
         <?php endif; ?>

         <?= form_open(url('/forms/submit'), 'POST', ['class' => 'demo-form']) ?>

         <div class="form-group">
            <?= form_component('form.input', [
               'name' => 'name',
               'label' => 'Name',
               'value' => old('name'),
               'placeholder' => 'John Doe',
               'required' => true,
               'className' => has_error('name') ? 'is-invalid' : '',
               'slot' => 'Minimum 3 characters, maximum 50'
            ]) ?>
         </div>

         <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <?= form_email('email', old('email'), ['placeholder' => 'john@example.com', 'class' => has_error('email') ? 'error' : '']) ?>
            <?= form_error('email') ?>
         </div>

         <div class="form-group">
            <?= form_component('form.input', [
               'name' => 'age',
               'type' => 'number',
               'label' => 'Age',
               'value' => old('age'),
               'placeholder' => '25',
               'required' => true,
               'min' => 18,
               'max' => 120,
               'className' => has_error('age') ? 'is-invalid' : '',
               'slot' => 'Must be between 18 and 120'
            ]) ?>
         </div>

         <div class="form-group">
            <label for="website">Website</label>
            <?= form_input('website', old('website'), ['placeholder' => 'https://example.com', 'class' => has_error('website') ? 'error' : '']) ?>
            <?= form_error('website') ?>
            <small>Optional, but must be valid URL if provided</small>
         </div>

         <div class="form-group">
            <label for="gender">Gender <span class="required">*</span></label>
            <?= form_select('gender', [
               '' => 'Select gender',
               'male' => 'Male',
               'female' => 'Female',
               'other' => 'Other'
            ], old('gender'), ['class' => has_error('gender') ? 'error' : '']) ?>
            <?= form_error('gender') ?>
         </div>

         <div class="form-group">
            <label for="password">Password <span class="required">*</span></label>
            <?= form_password('password', ['placeholder' => 'Enter password', 'class' => has_error('password') ? 'error' : '']) ?>
            <?= form_error('password') ?>
            <small>Min 8 chars, must include uppercase, lowercase, number, and special character</small>
         </div>

         <div class="form-group">
            <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
            <?= form_password('password_confirmation', ['placeholder' => 'Confirm password']) ?>
         </div>

         <div class="form-group">
            <label class="checkbox-label">
               <?= form_checkbox('terms', '1', false, ['class' => has_error('terms') ? 'error' : '']) ?>
               I agree to the terms and conditions <span class="required">*</span>
            </label>
            <?= form_error('terms') ?>
         </div>

         <div class="form-actions">
            <?= form_submit('Submit Form', ['class' => 'btn btn-primary']) ?>
            <a href="<?= url('/') ?>" class="btn">Cancel</a>
         </div>

         <?= form_close() ?>
         <?= component_close() ?>
      </section>

      <!-- Code Examples -->
      <section class="examples-section">
         <h2>💻 Code Examples</h2>

         <div class="example-block">
            <h3>Controller Validation</h3>
            <div class="code-example">
               <div class="code-example-header">PHP</div>
               <div class="code-block-wrapper">
                  <button class="copy-code-btn">Copy</button>
                  <pre><code class="language-php">// Verify CSRF
if (!CSRF::verify($_POST)) {
    redirect('/form');
}

// Validate form
$validator = Validator::make($_POST, [
    'name' => 'required|min:3|max:50',
    'email' => 'required|email',
    'password' => 'required|password|confirmed',
    'age' => 'required|numeric|min:18'
]);

if ($validator->fails()) {
    $_SESSION['errors'] = $validator->errors();
    $_SESSION['old'] = $_POST;
    redirect('/form');
}

// Get validated data
$data = $validator->validated();</code></pre>
               </div>
            </div>
         </div>

         <div class="example-block">
            <h3>Form Building</h3>
            <div class="code-example">
               <div class="code-example-header">PHP</div>
               <div class="code-block-wrapper">
                  <button class="copy-code-btn">Copy</button>
                  <pre><code class="language-php">&lt;?= Form::open('/submit', 'POST') ?&gt;
    
    &lt;?= Form::input('name', old('name')) ?&gt;
    &lt;?= Form::error('name') ?&gt;
    
    &lt;?= Form::email('email', old('email')) ?&gt;
    &lt;?= Form::error('email') ?&gt;
    
    &lt;?= Form::password('password') ?&gt;
    &lt;?= Form::error('password') ?&gt;
    
    &lt;?= Form::submit('Register') ?&gt;
    
&lt;?= Form::close() ?&gt;</code></pre>
               </div>
            </div>
         </div>

         <div class="example-block">
            <h3>Password Complexity Options</h3>
            <div class="code-example">
               <div class="code-example-header">config/validation.php</div>
               <div class="code-block-wrapper">
                  <button class="copy-code-btn">Copy</button>
                  <pre><code class="language-php">'password' => [
    'enabled' => true,
    'min_length' => 8,
    'require_uppercase' => true,
    'require_lowercase' => true,
    'require_numbers' => true,
    'require_special' => true,
]

// Custom per-field
'password' => 'required|password:min=12,upper,lower,number,special'

// Disable complexity
'password' => 'required|min:6'</code></pre>
               </div>
            </div>
         </div>
      </section>

      <p style="margin-top: 2rem;">
         <a href="<?= url('/') ?>" class="btn">Back to Home</a>
      </p>
   </div>
</div>

<style>
   .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin: 2rem 0;
   }

   .feature-item {
      background: var(--bg-secondary);
      padding: 1rem;
      border-radius: 8px;
      text-align: center;
      font-weight: 500;
   }

   .demo-form {
      background: var(--bg-secondary);
      padding: 2rem;
      border-radius: 12px;
      max-width: 600px;
      margin: 2rem 0;
   }

   .form-group {
      margin-bottom: 1.5rem;
   }

   .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--text-primary);
   }

   .form-group input,
   .form-group select,
   .form-group textarea {
      width: 100%;
      padding: 0.75rem;
      border: 2px solid var(--border-color);
      border-radius: 6px;
      background: var(--bg-primary);
      color: var(--text-primary);
      font-size: 1rem;
      transition: border-color 0.3s;
   }

   .form-group input:focus,
   .form-group select:focus,
   .form-group textarea:focus {
      outline: none;
      border-color: var(--accent-color);
   }

   .form-group input.error,
   .form-group select.error {
      border-color: #f44336;
   }

   .form-group small {
      display: block;
      margin-top: 0.25rem;
      font-size: 0.85rem;
      color: var(--text-secondary);
   }

   .required {
      color: #f44336;
   }

   .error-message {
      display: block;
      color: #f44336;
      font-size: 0.9rem;
      margin-top: 0.25rem;
   }

   .checkbox-label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
   }

   .checkbox-label input[type="checkbox"] {
      width: auto;
      cursor: pointer;
   }

   .form-actions {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
   }

   .alert {
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
   }

   .alert-error {
      background: #f44336;
      color: white;
   }
</style>

<script>
   console.log('Form validation demo loaded!');
</script>