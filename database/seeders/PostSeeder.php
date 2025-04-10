<?php
declare(strict_types=1);

namespace Database\Seeders;

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
        if (User::count() == 0) {
            $this->command->warn('No users found. Skipping PostSeeder. Please run UserSeeder first.');
            return;
        }

        Post::factory()->count(50)->mainPost()->create();
        Post::factory()->count(50)->comment()->create();

        $this->command->info('PostSeeder executed: Created 50 random posts.');
    }
}