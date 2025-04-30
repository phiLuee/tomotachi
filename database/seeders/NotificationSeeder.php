<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        if (User::count() == 0) {
            $this->command->warn('No users found. Skipping NotificationSeeder. Please run UserSeeder first.');
            return; 
        }

        // Erstelle 50 Notification-Datensätze
        Notification::factory()->count(50)->create();
    }
}
