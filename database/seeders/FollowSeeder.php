<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FollowSeeder extends Seeder
{
    public function run()
    {
        // Alle User holen
        $users = User::all();

        // Pivot-Tabelle "follows" leeren, damit wir keine doppelten Einträge generieren
        DB::table('follows')->truncate();

        // Für jeden User ...
        foreach ($users as $follower) {
            // ... wählen wir eine zufällige Anzahl anderer User (0 bis 5), denen er folgt
            $possibleFollowing = $users->where('id', '!=', $follower->id); // sich selbst nicht einschließen
            $randomFollows = $possibleFollowing->random(rand(0, 5));

            // Einträge in die "follows"-Tabelle schreiben
            foreach ($randomFollows as $followed) {
                DB::table('follows')->insert([
                    'follower_id' => $follower->id,
                    'following_id' => $followed->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
