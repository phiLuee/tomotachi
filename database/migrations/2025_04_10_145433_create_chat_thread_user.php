<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_thread_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chat_thread_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Rolle (z. B. "admin", "member", "readonly", etc.)
            $table->string('role')->default('member');

            // Timestamps für Beitritt
            $table->timestamps();

            // Ein User soll in einem Thread nur einmal vorkommen
            $table->unique(['chat_thread_id', 'user_id']);

            // Indexe
            $table->index('role');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_thread_user');
    }
};
