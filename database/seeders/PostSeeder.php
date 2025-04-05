<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Überprüfen, ob überhaupt User existieren
        if (User::count() == 0) {
            $this->command->warn('No users found. Skipping PostSeeder. Please run UserSeeder first.');
            return; // Beende den Seeder, wenn keine User da sind
        }

        Post::factory()->count(50)->create();

        $this->command->info('PostSeeder executed: Created 50 random posts.');
    }
}