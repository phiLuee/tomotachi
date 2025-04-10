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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')
                ->nullable();
            $table->string('content', 500); 

            $table->timestamps();

            $table->foreign('parent_id')
            ->references('id')
            ->on('posts')
            ->onDelete('cascade'); 

            $table->index('user_id');
            $table->index('parent_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
