# Akeneo PHP Lib

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aaron-apelt/akeneo-php-lib.svg?style=flat-square)](https://packagist.org/packages/aaron-apelt/akeneo-php-lib)
[![PHP Version Require](https://img.shields.io/packagist/php-v/aaron-apelt/akeneo-php-lib.svg?style=flat-square)](https://packagist.org/packages/aaron-apelt/akeneo-php-lib)
[![License](https://img.shields.io/packagist/l/aaron-apelt/akeneo-php-lib.svg?style=flat-square)](https://packagist.org/packages/aaron-apelt/akeneo-php-lib)

A modern, type-safe PHP library for interacting with the [Akeneo PIM API](https://api.akeneo.com/).
Designed for clean domain models, batch processing, and flexible (de)serialization.

---

## Features

- **Akeneo Object Abstractions**: Simple entity models for Akeneo objects.
- **Adapters**: Easy-to-use adapters for batch import/export with Akeneo.
- **Fluent Collections**: Powerful, lazy-evaluated collection operations.
- **Flexible Serialization**: Customizable serialization/denormalization.
- **Querying**: Query builder for advanced product searches.
- **Batch Upserts**: Efficient upsert and callback handling for large-scale imports.
- **Strict Types & Modern PHP**: Uses PHP 8+ features and strict typing throughout.

---

## Requirements

- PHP 8.1 or higher
- Composer

---

## Installation

```bash
composer require aaron-apelt/akeneo-php-lib
```

---

## Basic Usage

### Instantiate the Adapter

```php
use Akeneo\Pim\ApiClient\AkeneoPimClientBuilder;
use AkeneoLib\Adapter\ProductAdapter;
use AkeneoLib\Serializer\Serializer;

$clientBuilder = new AkeneoPimClientBuilder('https://your-akeneo-url.example.com');
$client = $clientBuilder
    ->buildAuthenticatedByPassword('client_id', 'secret', 'user', 'password');

$productApi = $client->getProductApi();
$serializer = new Serializer();

$adapter = new ProductAdapter($productApi, $serializer);
```

### Fetch Products

```php
foreach ($adapter->all() as $product) {
    // $product is an instance of AkeneoLib\Entity\Product
    echo $product->getIdentifier();
}
```

### Working with Fluent Collections

The `all()` method returns a `FluentAdapterResult` that supports lazy-evaluated collection operations:

```php
use AkeneoLib\Search\QueryParameter;

// Chain operations - only processes items as needed
$adapter->all()
    ->filter(fn($product) => $product->isEnabled())
    ->map(fn($product) => $product->getIdentifier())
    ->take(100)
    ->toArray();

// Pagination
$page2 = $adapter->all()
    ->skip(50)
    ->take(50)
    ->toArray();

// Execute side effects while maintaining chain ability
$adapter->all()
    ->each(fn($product) => logger()->info("Processing {$product->getIdentifier()}"))
    ->filter(fn($product) => $product->getFamily() === 'electronics')
    ->toArray();

// Terminal operations
$firstEnabled = $adapter->all()->first(fn($p) => $p->getEnabled());
$lastProduct = $adapter->all()->last();

// Practical reduce example - collect all identifiers
$identifiers = $adapter->all()->reduce(
    fn($acc, $product) => [...$acc, $product->getIdentifier()], 
    []
);
```

#### Available Methods

**Lazy Operations** (maintain lazy evaluation):
- `filter(callable $callback)` - Filter items
- `map(callable $callback)` - Transform items
- `each(callable $callback)` - Execute side effects
- `take(int $limit)` - Limit to first N items
- `skip(int $count)` - Skip first N items
- `chunk(int $size)` - Split into chunks

**Terminal Operations** (materialize the collection):
- `toArray()` - Convert to array
- `first(?callable $callback = null)` - Get first item
- `last(?callable $callback = null)` - Get last item
- `reduce(callable $callback, $initial)` - Reduce to single value
- `sort(callable $callback)` - Sort items (⚠️ loads all into memory)
- `unique(?callable $callback = null)` - Filter duplicates (⚠️ loads all into memory)

**Note**: Operations marked with ⚠️ materialize the entire collection into memory. Use with caution on large datasets from API cursors.

### Upsert Products in Batches

```php
$product = new AkeneoLib\Entity\Product('my-sku');
// ...set properties, values, etc.

$adapter->stage($product); // Will push automatically when batch size is reached
$adapter->push();          // Manually push any remaining staged products
```

### Custom Response Callback

```php
$adapter->onResponse(function ($response, $products, $dateTime) {
    // Handle API response or log batch import results here
});
```

---

## Advanced

- **Querying**: Use `AkeneoLib\Search\QueryParameter` to filter, sort, and paginate.
- **Custom Serialization**: Swap out or configure the serializer.

---

## Testing

This library is tested with [Pest](https://pestphp.com/).  
To run tests:

```bash
composer install
composer test
```

Individual test suites:

```bash
# Run only unit tests
composer test:unit

# Run only linting check
composer test:lint

# Run only static analysis
composer analysis
```

---

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

---

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for details on what has changed.

---

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
