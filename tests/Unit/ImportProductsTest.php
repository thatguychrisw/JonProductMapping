<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class ImportProductsTest extends TestCase
{
    use DatabaseTransactions;

    const COMMAND = 'import:products';

    protected function setUp()
    {
        parent::setUp();

        Carbon::setTestNow();
    }

    /**
     * @test
     */
    public function it_requires_a_prices_and_categories_argument()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageRegExp('/missing.*products.*categories/');

        $this->artisan(self::COMMAND);
    }

    /**
     * @test
     */
    public function it_imports_products_to_the_database()
    {
        $this->artisan(self::COMMAND, [
            'products' => base_path('tests/fixtures/products.csv'),
            'categories' => base_path('tests/fixtures/categories.csv'),
        ]);

        $this->assertDatabaseHas('products', [
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        $this->assertDatabaseHas('categories', [
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
