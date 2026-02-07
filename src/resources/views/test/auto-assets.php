<?php

declare(strict_types=1); ?>
<h1>Automatic Asset Injection Test</h1>
<p id="test-element">This text should be green if CSS is injected.</p>
<p>Check the console for a "JS Injected!" message.</p>

<hr>

<h2>Component Test</h2>
<?= \App\App\Core\View::component('test.card', ['title' => 'My Component Title']) ?>