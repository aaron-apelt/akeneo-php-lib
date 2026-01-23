# Contributing to Akeneo PHP Lib

Thank you for your interest in contributing to Akeneo PHP Lib! We welcome contributions from the community.

## Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/akeneo-php-lib.git`
3. Install dependencies: `composer install`
4. Create a new branch: `git checkout -b feature/your-feature-name`

## Development Workflow

### Running Tests

```bash
# Run all tests (linting, unit tests, and static analysis)
composer test

# Run only unit tests
composer test:unit

# Run only linting check
composer test:lint

# Run only static analysis
composer analysis
```

### Code Style

This project uses [Laravel Pint](https://laravel.com/docs/pint) for code formatting. The coding standard is defined in `pint.json`.

```bash
# Check code style
composer test:lint

# Fix code style issues
composer lint
```

### Static Analysis

We use [PHPStan](https://phpstan.org/) at level 5 for static analysis.

```bash
composer analysis
```

## Pull Request Process

1. Ensure all tests pass: `composer test`
2. Update the CHANGELOG.md with details of your changes
3. Update the README.md if you're adding new features
4. Add or update tests for your changes
5. Make sure your code follows the existing code style
6. Create a pull request with a clear description of your changes

## Coding Standards

- Follow PSR-12 coding standards
- Use strict types declaration in all PHP files
- Write comprehensive tests for new features
- Document public APIs with PHPDoc comments
- Keep methods focused and concise
- Prefer immutability where possible

## Testing Guidelines

- Write tests using [Pest](https://pestphp.com/)
- Aim for high code coverage
- Test both success and failure cases
- Use descriptive test names
- Mock external dependencies (API clients, etc.)

## Commit Messages

- Use clear and descriptive commit messages
- Start with a verb in present tense (e.g., "Add", "Fix", "Update")
- Reference issue numbers when applicable

## Questions?

If you have questions, feel free to:
- Open an issue for discussion
- Review existing issues and pull requests

## Code of Conduct

Please be respectful and constructive in all interactions. We aim to maintain a welcoming and inclusive community.
