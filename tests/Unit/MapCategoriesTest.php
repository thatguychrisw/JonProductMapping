<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class ImportProductsTest extends TestCase
{
    use DatabaseTransactions;

    const COMMAND = 'map:categories';

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function it_runs()
    {
        $this->artisan(self::COMMAND);
    }
}
