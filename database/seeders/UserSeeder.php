<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    private int $userCount = 50;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin@example.com'),
        ]);

        $admin->assignRole('admin');
        $admin->profile->update([
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($admin->username) . '&size=128&background=random',
            'bio' => 'Ich bin der Admin.',
            'location' => 'Berlin',
            'website' => 'https://admin.example.com',
        ]);

        $user = User::factory()->create([
            'name' => 'User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('user@example.com'),
        ]);

        $user->assignRole('user');
        $user->profile->update([
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&size=128&background=random',
            'bio' => 'Ich bin ein Testuser.',
            'location' => 'Hamburg',
            'website' => 'https://user.example.com',
        ]);

        User::factory()
            ->count($this->userCount)
            ->create()
            ->each(function ($user) {
                $user->assignRole('user');
                $user->profile->update([
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&size=128&background=random',
                    'bio' => fake()->sentence(),
                    'location' => fake()->city(),
                    'website' => fake()->url(),
                ]);
            });

        $this->command->info('UserSeeder executed: Created test users and ' . $this->userCount . ' random users.');
    }
}
