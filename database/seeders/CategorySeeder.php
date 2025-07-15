<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Makanan']);
        Category::create(['name' => 'Minuman']);
        Category::create(['name' => 'Bumbu Dapur']);
        Category::create(['name' => 'Peralatan Mandi']);
        Category::create(['name' => 'Pembersih Rumah']);
    }
}
