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
    protected $signature = 'map:categories {--a|all}';

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
        $scope = $this->option('all') ? "all products" : "un-mapped products only";
        $this->alert("Running map:categories; for {$scope}");

        $this->mapProductsToCategories();

        $this->warnUnmapped();

        $this->line('Finished.');
    }

    public function mapProductsToCategories()
    {
        $this->line('Mapping products to categories.');

        $this->info('   Warming up progress bar.');
        $bar = new ProgressBar($this->getOutput(), $this->productQuery()->count());

        $this->productQuery()->chunk(5000, function (Collection $products) use ($bar) {
            $products->each(function (Product $product) {
                $categoryPermutations = $this->getCategoryPermutations($product);

                $category = null;
                foreach ($categoryPermutations as $permutation) {
                    list($categoryId, $subCategoryId) = $permutation;

                    $category = (new Category)
                        ->byCpuCode($product->data->cpuCode)
                        ->byCategoryId($categoryId)
                        ->bySubCategoryId($subCategoryId)
                        ->first();

                    if ($category) break;
                }

                if ($category) {
                    $productData = (array) $product->data;
                    $productData['category'] = $category;

                    $product->data = (object) $productData;

                    $product->save();
                }
            });

            $bar->advance($products->count());
        });

        $this->line("\n Done. \n");
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

            case 2:
                $permutations->push([$category{0}, $category{1}]);
                $permutations->push([$category{1}, $category{0}]);
                $permutations->push([$category, 0]);
                $permutations->push([0, $category]);

                break;

            case 1:
                $permutations->push([$category, 0]);
                $permutations->push([0, $category]);

                break;
        }

        $permutations->transform(function(array $permutation) {
            return array_map('intval', $permutation);
        });

        return $permutations;
    }

    /**
     * @return Product|\Illuminate\Database\Eloquent\Builder
     */
    public function productQuery()
    {
        $productsQuery = (new Product);
        if (!$this->option('all')) {
            $productsQuery = $productsQuery->unmappedCategories();
        }

        return $productsQuery;
    }

    public function warnUnmapped()
    {
        $this->line('Checking for unmapped products.');

        $numUnmapped = (new Product)->unmappedCategories()->count();
        if ($numUnmapped > 0) {
            $this->warn("   {$numUnmapped} products were unable to be mapped.");
        }

        $this->line("Done. \n");
    }
}
