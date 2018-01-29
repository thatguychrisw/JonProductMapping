<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Category
 *
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category byCpuCode($cpuCode)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category byCategoryId($categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category bySubCategoryId($subCategoryId)
 */
class Category extends Model implements FromDataFile
{
    protected $casts = ['data' => 'object'];
    protected $fillable = ['data', 'created_at', 'updated_at'];
    protected $table = 'categories';

    public static function fromDataFile(array $record)
    {
        $headers = [
            'item',
            'categoryId',
            'subCategoryId',
            'cpuCode',
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
     * @param $cpuCode
     * @return static|Builder
     */
    public function scopeByCpuCode(Builder $query, $cpuCode)
    {
        return $query->whereRaw("data->>'cpuCode' = '$cpuCode'");
    }

    public function scopeByCategoryId(Builder $query, $categoryId)
    {
        return $query->whereRaw("data->>'categoryId' = '$categoryId'");
    }

    public function scopeBySubCategoryId(Builder $query, $subCategoryId)
    {
        return $query->whereRaw("data->>'subCategoryId' = '$subCategoryId'");
    }
}
