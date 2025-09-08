<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        

        User::factory()->create([
            'id' => uuid_create(),
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed categories
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
        ]);
    }
}
