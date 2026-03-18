# Contributing

Contributions are welcome and appreciated.

## Development Setup

1. Clone the repository
2. Install dependencies:

```bash
composer install
```

3. Run tests:

```bash
vendor/bin/phpunit
```

## Pull Requests

- Fork the repository and create your branch from `main`.
- Add tests for any new functionality.
- Ensure the test suite passes.
- Follow PSR-12 coding standards.
- Use `declare(strict_types=1)` in every PHP file.

## Coding Standards

- PHP 8.2+
- PSR-12
- Strict types
- Typed properties and return types
- Dependency injection
- SOLID principles

## Testing

- All new features must include unit tests.
- Use Orchestra Testbench for Laravel integration tests.
- Target coverage >= 90%.

## Reporting Issues

Use GitHub Issues to report bugs. Include:

- PHP version
- Laravel version
- Steps to reproduce
- Expected vs actual behavior
