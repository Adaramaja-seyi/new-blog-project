<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology'],
            ['name' => 'Lifestyle'],
            ['name' => 'Travel'],
            ['name' => 'Food'],
            ['name' => 'Health'],
            ['name' => 'Education'],
            ['name' => 'Business'],
            ['name' => 'Entertainment'],
            ['name' => 'Sports'],
            ['name' => 'Fashion'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
            ]);
        }
    }
}
