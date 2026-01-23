# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial public release
- Akeneo object abstractions for entities (Product, Category, Attribute, etc.)
- Adapters for batch import/export with Akeneo API
- Fluent collection operations with lazy evaluation
- Flexible serialization/denormalization support
- Query builder for advanced product searches
- Batch upsert capabilities with callback handling
- Comprehensive test suite with Pest
- Code quality tools (PHPStan, Laravel Pint)
- MIT License
- Complete documentation

### Changed
- Changed license from proprietary to MIT for public release

### Fixed
- Fixed test infrastructure for proper global helper functions
- Fixed PHP method chaining syntax in tests
- Fixed undefined function errors in ValuesDenormalizer

[Unreleased]: https://github.com/aaron-apelt/akeneo-php-lib/compare/v1.0.0...HEAD
