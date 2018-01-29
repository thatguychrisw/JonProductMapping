# JonProductMapping

## Import products
```sh
php artisan import:products {--truncate} /path/to/products.csv /path/to/categories.csv
```

## Map products to categories
Using the `--a|all` option will force a re-mapping of every product.
```sh
php artisan map:categories {--a|all}
```

## Query to retrieve all un-mapped products
```sql
select
  data->>'cpuCode' cpuCode,
  data->>'ingramCategorySubCategory' ingramCategory,
  data->>'category' category
from products where (data->'category' is null);
```
