<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'PHP', 'color' => '#4F5B93'],
            ['name' => 'Laravel', 'color' => '#FF2D20'],
            ['name' => 'Vue.js', 'color' => '#4FC08D'],
            ['name' => 'JavaScript', 'color' => '#F7DF1E'],
            ['name' => 'Tutorial', 'color' => '#6c757d'],
            ['name' => 'Tips', 'color' => '#28a745'],
            ['name' => 'Guide', 'color' => '#17a2b8'],
            ['name' => 'Review', 'color' => '#ffc107'],
            ['name' => 'News', 'color' => '#dc3545'],
            ['name' => 'Opinion', 'color' => '#6f42c1'],
            ['name' => 'Web Development', 'color' => '#fd7e14'],
            ['name' => 'Frontend', 'color' => '#20c997'],
            ['name' => 'Backend', 'color' => '#6610f2'],
            ['name' => 'API', 'color' => '#e83e8c'],
            ['name' => 'Database', 'color' => '#795548'],
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag['name'],
                'slug' => Str::slug($tag['name']),
                'color' => $tag['color']
            ]);
        }
    }
}
