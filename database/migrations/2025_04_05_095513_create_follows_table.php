<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            // Foreign key für den User, der folgt (follower)
            $table->foreignId('follower_id')
                  ->constrained('users') // Verweist auf die 'id' in der 'users'-Tabelle
                  ->onDelete('cascade'); // Wenn der User gelöscht wird, wird auch der Follow-Eintrag gelöscht

            // Foreign key für den User, dem gefolgt wird (following)
            $table->foreignId('following_id')
                  ->constrained('users') // Verweist auf die 'id' in der 'users'-Tabelle
                  ->onDelete('cascade'); // Wenn der User gelöscht wird, wird auch der Follow-Eintrag gelöscht

            $table->timestamps(); // Fügt created_at und updated_at hinzu

            // Zusammengesetzter Primärschlüssel, um Duplikate zu verhindern
            // Ein User kann einem anderen User nur einmal folgen.
            $table->primary(['follower_id', 'following_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};