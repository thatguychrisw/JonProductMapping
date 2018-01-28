<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class MapCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->alert('Mapping products to categories.');

        $this->mapProductsToCategories();

        $this->warnUnmapped();

        $this->line('Finished.');
    }

    public function mapProductsToCategories(): void
    {
        $this->info('   Warming up progress bar.', 1);
        $bar = new ProgressBar($this->getOutput(), (new Product)->unmappedCategories()->count());

        (new Product)->unmappedCategories()->chunk(5000, function (Collection $products) use ($bar) {
            $products->each(function (Product $product) {
                $categoryPermutations = $this->getCategoryPermutations($product);

                $categoryGroup = (new Category)->byCpuCode($product->data->cpuCode);

                $category = null;
                foreach ($categoryPermutations as $permutation) {
                    list($categoryId, $subCategoryId) = $permutation;

                    $category = $categoryGroup->byCategoryId($categoryId)->bySubCategoryId($subCategoryId)->first();

                    if ($category) break;
                }

                if ($category) {
                    $productData = (array) $product->data;
                    $productData['category'] = $category;

                    $product->data = (object) $productData;

                    $product->save();
                }
            });

            $bar->advance(5000);
        });
    }

    private function getCategoryPermutations(Product $product)
    {
        $permutations = collect();

        $category = $product->data->ingramCategorySubCategory;

        switch(strlen($category))
        {
            case 4:
                $permutations->push(str_split($category, 2));

                break;

            case 3:
                $permutations->push([$category{0}, substr($category, 1)]);
                $permutations->push([substr($category, 0, -1), $category{2}]);

                break;
        }

        return $permutations;
    }

    public function warnUnmapped()
    {
        $numUnmapped = (new Product)->unmappedCategories()->count();
        if ($numUnmapped > 0) {
            $this->warn("{$numUnmapped} products were unable to be mapped.");
        }
    }
}
