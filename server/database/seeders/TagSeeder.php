<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $tags = [
            ['name' => 'Technology'],
            ['name' => 'Health & Wellness'],
            ['name' => 'Travel'],
            ['name' => 'Food & Recipes'],
            ['name' => 'Education'],
            ['name' => 'Business & Finance'],
            ['name' => 'Entertainment'],
            ['name' => 'Sports'],
            ['name' => 'Fashion & Style'],
            ['name' => 'Science & Innovation'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
