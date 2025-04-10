<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chat_thread_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->text('content', 500); // Maximal 500 Zeichen

            // Optional: z. B. "read_at" wenn nur 1 Empfänger existiert 
            // (direkte Chats). Für mehrere Empfänger braucht man 
            // evtl. eine separate "message_read" Tabelle. 
            // $table->timestamp('read_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexe
            $table->index('chat_thread_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
