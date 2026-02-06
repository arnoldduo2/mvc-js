# Changelog

All notable changes to the MVC-JS Framework will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Enhanced base Controller class with comprehensive common methods:
  - Session management (`session()`, `setSession()`, `hasSession()`, `forgetSession()`)
  - Flash message support (`flash()`, `getFlash()`, `hasFlash()`)
  - Request helpers (`all()`, `only()`, `except()`, `has()`, `filled()`, `isPost()`, `isGet()`, `method()`)
  - Response helpers (`success()`, `error()`)
  - Redirect helpers (`back()`, `redirectWith()`)
  - File upload handling (`hasFile()`, `file()`, `moveFile()`, `validateFile()`)
  - Authorization helpers (`isAuthenticated()`, `userId()`, `user()`, `requireAuth()`, `requireGuest()`)
  - Utility methods (`abort()`, `paginate()`)
  - Enhanced validation integration (`validateOrFail()`, `verifyCsrf()`, `verifyCsrfOrFail()`)

## [0.1.0-alpha] - 2026-02-06

### Added

- Initial framework architecture with MVC pattern
- Singleton Application bootstrap system
- Laravel-style routing with fluent API
- Route groups with middleware support
- Route parameters and dynamic routing
- Base Model class with fluent query builder
- Mass assignment protection in models
- Automatic timestamp management (created_at, updated_at)
- Soft delete functionality for models
- Single Page Application (SPA) system with server-side rendering
- AJAX request interception for seamless navigation
- Frontend router with browser history management
- JSON response format for SPA page updates
- CSRF protection system
- Form validation with comprehensive rules
- Password complexity validation
- Bcrypt password hashing
- Input sanitization and XSS protection
- Helper functions for common tasks
- URL/base path system for subdirectory deployment
- Dark mode implementation with persistence
- User-friendly error pages (404, 403) with SPA awareness
- Modern SVG favicon with gradient design
- Responsive layout system
- Loading indicators for SPA transitions
- Form builder utility class
- Cache system for performance optimization
- Middleware pipeline (Authentication, Authorization, Role, Permission)
- View rendering system with layouts and partials
- Environment configuration with .env support
- Comprehensive .gitignore for PHP projects
- Documentation (README.md, ARCHITECTURE.md, CONTRIBUTING.md, AUTHORS.md)
- Demo pages showcasing framework features

### Changed

- N/A (Initial release)

### Deprecated

- N/A (Initial release)

### Removed

- N/A (Initial release)

### Fixed

- N/A (Initial release)

### Security

- Implemented CSRF token validation
- Added input sanitization
- Secure password hashing with bcrypt
- XSS protection in views

---

## Version History

- **0.1.0-alpha** (2026-02-06) - Initial alpha pre-release

[Unreleased]: https://github.com/yourusername/mvc-js/compare/v0.1.0-alpha...HEAD
[0.1.0-alpha]: https://github.com/yourusername/mvc-js/releases/tag/v0.1.0-alpha
