<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            // Polymorphe Felder: mediable_id und mediable_type
            // Typischerweise: "App\Models\Post" / "App\Models\Profile" etc.
            $table->morphs('mediable');

            // Falls du nachvollziehen willst, wem die Datei "gehört":
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('role')->nullable();

            // Welches "Disk"-Setup (local, public, s3, etc.)
            $table->string('disk')->default('public');

            // Wo liegt die Datei auf dem gewählten Disk?
            $table->string('file_path');

            // MIME-Type (z. B. "image/png", "video/mp4")
            $table->string('mime_type')->nullable();

            // Dateigröße in Bytes (zur Anzeige oder Validierung)
            $table->unsignedBigInteger('file_size')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
};
