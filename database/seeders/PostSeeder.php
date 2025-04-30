<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{

    private int $postsCount = 100;
    private int $commentsCount = 200;


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() == 0) {
            $this->command->warn('No users found. Skipping PostSeeder. Please run UserSeeder first.');
            return;
        }

        Post::factory()->count($this->postsCount)->mainPost()->create();
        Post::factory()->count($this->commentsCount)->comment()->create();

        $this->command->info('PostSeeder executed: Created ' . $this->postsCount . ' random posts and ' . $this->commentsCount . ' comments.');
    }
}