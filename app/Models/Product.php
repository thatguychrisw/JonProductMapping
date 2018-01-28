<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 *
 * @package App\Models
 * @mixin \Eloquent
 * @method static Builder|\App\Models\Product unmappedCategories()
 */
class Product extends Model implements FromDataFile
{
    protected $casts = ['data' => 'object'];
    protected $fillable = ['data', 'created_at', 'updated_at'];

    public static function fromDataFile(array $record)
    {
        $headers = [
            'actionIndicator',
            'ingramPartNumber',
            'vendorNumber',
            'vendorName',
            'ingramPartDescription1',
            'ingramPartDescription2',
            'retailPrice',
            'vendorPartNumber',
            'weight',
            'upcCode',
            'length',
            'width',
            'height',
            'priceChangeFlag',
            'customerPrice',
            'specialPriceFlag',
            'availabilityFlag',
            'status',
            'cpuCode',
            'mediaType',
            'ingramCategorySubCategory',
            'newItemReceiptFlag',
            'instantRebate',
            'substitutePartNumber',
        ];

        $now = Carbon::now();

        return (new self())->fill([
            'data' => json_encode(array_combine($headers, array_map('trim', array_values($record)))),
            'created_at' => $now->toDateTimeString(),
            'updated_at' => $now->toDateTimeString(),
        ]);
    }

    /**
     * @param Builder $query
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function scopeUnmappedCategories(Builder $query)
    {
        return $query->whereNull('data->categories');
    }
}
