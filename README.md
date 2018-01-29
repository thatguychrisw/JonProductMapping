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

## Query to find all matched products
```sql
select
  data->>'actionIndicator' action_indicator,
  data->>'ingramPartNumber' ingram_part_number,
  data->>'vendorNumber' vendor_number,
  data->>'vendorName' vendor_name,
  data->>'ingramPartDescription1' ingram_part_description_1,
  data->>'ingramPartDescription2' ingram_part_description_2,
  data->>'retailPrice' retail_price,
  data->>'vendorPartNumber' vendor_part_number,
  data->>'weight' weight,
  data->>'upcCode' upc_code,
  data->>'length' length,
  data->>'width' width,
  data->>'height' height,
  data->>'priceChangeFlag' price_change_flag,
  data->>'customerPrice' customer_price,
  data->>'specialPriceFlag' special_price_flag,
  data->>'availabilityFlag' availability_flag,
  data->>'status' status,
  data->>'cpuCode' cpu_code,
  data->>'mediaType' media_type,
  data->>'ingramCategorySubCategory' ingram_category_sub_category,
  data->>'newItemReceiptFlag' new_item_receipt_flag,
  data->>'instantRebate' instant_rebate,
  data->>'substitutePartNumber' substitute_part_number,
  data->'category'->'data'->>'item' category_matched_item,
  data->'category'->'data'->>'cpuCode' category_matched_cpu_code,
  data->'category'->'data'->>'categoryId' category_matched_category_id,
  data->'category'->'data'->>'subCategoryId' category_matched_sub_category_id,
  data->>'category' category_matched
from products where (data->'category' is not null);
```
