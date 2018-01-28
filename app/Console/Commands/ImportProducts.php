<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\FromDataFile;
use App\Models\Product;
use Eloquent;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use SplFileObject;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {--truncate} {products} {categories}';

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
     */
    public function handle()
    {
        $this->alert('Importing products and categories.');

        if ($this->option('truncate')) {
            $this->line("Truncating products and categories tables.\n");

            Product::query()->truncate();
            Category::query()->truncate();
        }

        $this->line('Importing products.');
        $this->importDataFile($this->argument('products'), new Product);
        $this->line("\n");

        $this->line('Importing categories.');
        $this->importDataFile($this->argument('categories'), new Category);
        $this->line("\n");

        $this->line('Finished.');
    }

    /**
     * Imports data from a csv file using a model
     *
     * @param $file
     * @param FromDataFile|Model|Eloquent $model
     */
    public function importDataFile($file, FromDataFile $model)
    {
        $csv = new \SplFileObject($file);
        $csv->setFlags(SplFileObject::READ_CSV);

        $this->info('   Warming up progress bar.', 1);
        $bar = new ProgressBar($this->getOutput(), $this->getTotalLines($csv));

        $batched = collect();

        $csv->seek(1);
        while (!$csv->eof()) {
            if (count($csv->current()) <= 1) {
                $bar->advance();

                continue;
            }

            $batched->push($model::fromDataFile($csv->current()));

            if ($batched->count() % 1000 === 0) {
                $model::insert($batched->toArray());

                $bar->advance(1000);

                $batched = collect();
            }

            $csv->next();
        }

        if ($batched->count() > 0) {
            $model::insert($batched->toArray());

            $bar->advance($batched->count());
        }
    }

    /**
     * @param SplFileObject $file
     * @return int
     */
    private function getTotalLines(SplFileObject $file)
    {
        $lastLineNumber = $file->key();

        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();

        $file->seek($lastLineNumber);

        return $totalLines;
    }

}
