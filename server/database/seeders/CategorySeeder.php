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
            [
                'name' => 'Technology',
                'description' => 'Articles about technology, programming, and digital innovation',
                'color' => '#007bff'
            ],
            [
                'name' => 'Lifestyle',
                'description' => 'Personal development, habits, and life improvement',
                'color' => '#28a745'
            ],
            [
                'name' => 'Travel',
                'description' => 'Travel guides, tips, and adventure stories',
                'color' => '#ffc107'
            ],
            [
                'name' => 'Food',
                'description' => 'Recipes, cooking tips, and culinary experiences',
                'color' => '#dc3545'
            ],
            [
                'name' => 'Health',
                'description' => 'Wellness, fitness, and mental health topics',
                'color' => '#20c997'
            ],
            [
                'name' => 'Education',
                'description' => 'Learning resources, tutorials, and educational content',
                'color' => '#6f42c1'
            ],
            [
                'name' => 'Business',
                'description' => 'Entrepreneurship, marketing, and business insights',
                'color' => '#fd7e14'
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Movies, music, books, and pop culture',
                'color' => '#e83e8c'
            ],
            [
                'name' => 'Sports',
                'description' => 'Sports news, analysis, and fitness content',
                'color' => '#6c757d'
            ],
            [
                'name' => 'Fashion',
                'description' => 'Style tips, trends, and fashion advice',
                'color' => '#17a2b8'
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'color' => $categoryData['color'],
                'is_active' => true
            ]);
        }
    }
}
