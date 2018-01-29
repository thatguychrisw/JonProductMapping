# JonProductMapping

## Import products
```sh
php artisan import:products {--truncate} /path/to/products.csv /path/to/categories.csv
```

## Map products to categories
Using the `--a|all` option will force a re-mapping of every product.
```sh
php artisan map:products {--a|all}
```
