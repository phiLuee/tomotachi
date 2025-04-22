<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin@example.com'),
        ]);

        $user->assignRole('admin');

        $user = User::factory()->create([
            'name' => 'User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('user@example.com'),
        ]);

        $user->assignRole('admin');

        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) {
                $user->assignRole('user');
            });;

        $this->command->info('UserSeeder executed: Created test user and 10 random users.');
    }
}
