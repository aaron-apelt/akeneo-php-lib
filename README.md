# Akeneo PHP Lib

A modern, type-safe PHP library for interacting with the [Akeneo PIM API](https://api.akeneo.com/).
Designed for clean domain models, batch processing, and flexible (de)serialization.

---

## Features

- **Akeneo Object Abstractions**: Simple, immutable entity models for Akeneo objects.
- **Adapters**: Easy-to-use adapters for batch import/export with Akeneo.
- **Flexible Serialization**: Customizable serialization/denormalization.
- **Querying**: Query builder for advanced product searches.
- **Batch Upserts**: Efficient upsert and callback handling for large-scale imports.
- **Strict Types & Modern PHP**: Uses PHP 8+ features and strict typing throughout.

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
vendor/bin/pest
```
