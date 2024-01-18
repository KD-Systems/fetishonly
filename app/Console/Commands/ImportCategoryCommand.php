<?php

namespace App\Console\Commands;

use App\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportCategoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $csvData = [];

        $csv = Storage::disk('local')->path('import/categories.csv');

        $csvFile = fopen($csv, 'r');

        while(($data = fgetcsv($csvFile, 1000, ",")) !== false) {
            $csvData[] = $data[0];
        }

        $categories = [];

        foreach($csvData as $key=>$value) {
            $categories[] = [
                'name' => $value,
                'slug' => Str::slug($value),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Category::truncate();

        Category::insert($categories);

        return 0;
    }
}
