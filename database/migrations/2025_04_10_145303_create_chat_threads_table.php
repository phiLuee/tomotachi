<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_threads', function (Blueprint $table) {
            $table->id();
            
            // z. B. "group" oder "direct"
            // $table->string('type')->default('direct')
            //       ->comment('Unterscheidung zwischen Gruppen- oder Direktchat');

            // Name des Chats, meist nur sinnvoll für Gruppenchats
            $table->string('name')->nullable();

            // Optional: Ein Bild/Avatar für den Gruppenchat
            // $table->string('avatar')->nullable();

            // Falls du Kanäle/Kommunikation archivieren möchtest:
            // $table->boolean('is_archived')->default(false);

            // Timestamps
            $table->timestamps();

            // Index auf type, falls du oft nach Gruppenchats filterst
            // $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_threads');
    }
};
