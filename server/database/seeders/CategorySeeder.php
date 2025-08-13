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
            ['name' => 'Technology', 'description' => 'Posts about technology and programming', 'color' => '#007bff'],
            ['name' => 'Lifestyle', 'description' => 'Lifestyle and personal posts', 'color' => '#28a745'],
            ['name' => 'Travel', 'description' => 'Travel experiences and guides', 'color' => '#17a2b8'],
            ['name' => 'Food', 'description' => 'Food recipes and reviews', 'color' => '#ffc107'],
            ['name' => 'Health', 'description' => 'Health and wellness posts', 'color' => '#dc3545'],
            ['name' => 'Business', 'description' => 'Business and entrepreneurship', 'color' => '#6f42c1'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'color' => $category['color'],
                'is_active' => true
            ]);
        }
    }
}
