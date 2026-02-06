# Contributing to MVC-JS Framework

First off, thank you for considering contributing to MVC-JS! It's people like you that make MVC-JS such a great tool.

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps which reproduce the problem**
- **Provide specific examples to demonstrate the steps**
- **Describe the behavior you observed after following the steps**
- **Explain which behavior you expected to see instead and why**
- **Include screenshots if possible**

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Use a clear and descriptive title**
- **Provide a step-by-step description of the suggested enhancement**
- **Provide specific examples to demonstrate the steps**
- **Describe the current behavior and explain which behavior you expected to see instead**
- **Explain why this enhancement would be useful**

### Pull Requests

- Fill in the required template
- Do not include issue numbers in the PR title
- Follow the PHP and JavaScript style guides
- Include thoughtfully-worded, well-structured tests
- Document new code
- End all files with a newline

## Development Process

### Setup Development Environment

```bash
# Clone your fork
git clone https://github.com/your-username/mvc-js.git
cd mvc-js

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Configure your database
# Edit .env file with your database credentials
```

### Coding Standards

#### PHP

- Follow PSR-12 coding standard
- Use strict types: `declare(strict_types=1);`
- Use type hints for all parameters and return values
- Write PHPDoc comments for all classes and methods
- Keep methods small and focused (Single Responsibility Principle)

Example:

```php
<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;
use App\App\Core\View;

/**
 * Example Controller
 */
class ExampleController extends Controller
{
    /**
     * Display the index page
     *
     * @return void
     */
    public function index(): void
    {
        View::page('pages/example', [
            'title' => 'Example Page'
        ]);
    }
}
```

#### JavaScript

- Use ES6+ features
- Use ES6 modules
- Use meaningful variable names
- Add JSDoc comments for complex functions
- Keep functions small and focused

Example:

```javascript
/**
 * Navigate to a new URL
 * @param {string} url - The URL to navigate to
 */
navigate(url) {
    window.history.pushState({ url }, "", url);
    this.loadPage(url, true);
}
```

#### CSS

- Use CSS variables for theming
- Follow BEM naming convention when appropriate
- Keep selectors simple and performant
- Group related properties together
- Add comments for complex styles

### Testing

- Write tests for new features
- Ensure all tests pass before submitting PR
- Aim for high code coverage
- Test both happy path and edge cases

```bash
# Run tests (when implemented)
composer test
```

### Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests liberally after the first line

Example:

```
Add dark mode toggle functionality

- Implement CSS variables for theming
- Add toggle button to navbar
- Persist preference to localStorage
- Update all components for dark mode support

Fixes #123
```

### Branch Naming

- `feature/` - New features
- `bugfix/` - Bug fixes
- `hotfix/` - Urgent fixes
- `docs/` - Documentation changes
- `refactor/` - Code refactoring

Example: `feature/add-user-authentication`

## Project Structure

```
mvc-js/
├── src/
│   ├── App/
│   │   ├── Controllers/    # Add new controllers here
│   │   ├── Core/          # Core framework (be careful!)
│   │   └── Models/        # Add new models here
│   ├── Helpers/           # Helper functions
│   ├── config/            # Configuration files
│   ├── resources/         # Frontend resources
│   │   ├── css/          # Stylesheets
│   │   ├── js/           # JavaScript modules
│   │   └── views/        # View templates
│   └── routes/           # Route definitions
```

## What Should I Know Before I Get Started?

### Architecture

- MVC-JS follows the MVC (Model-View-Controller) pattern
- The router uses a Laravel-style fluent API
- Models use an Active Record pattern with Query Builder
- The SPA system uses server-side rendering with client-side navigation
- Error handling is centralized through the Anode Error Handler

### Key Components

- **Router** - Handles routing and middleware
- **Model** - Database interaction and business logic
- **View** - Template rendering and JSON responses
- **Controller** - Request handling and coordination
- **Application** - Bootstrap and lifecycle management

## Recognition

Contributors will be added to the [AUTHORS.md](AUTHORS.md) file.

## Questions?

Feel free to open an issue with your question or contact the maintainers directly.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to MVC-JS! 🎉
