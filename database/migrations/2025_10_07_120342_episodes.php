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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('podcast_id')->constrained('podcasts')->onDelete('cascade');
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->foreignId('transcript_id')->nullable();

            // Episode details
            $table->integer('episode_number')->nullable();
            $table->string('title');
            $table->string('slug', 200)->unique();
            $table->mediumText('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->boolean('explicit')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();

            // Media fields
            $table->string('cover_image', 500)->nullable();
            $table->string('audio_url')->nullable();   // hosted audio
            $table->string('video_url')->nullable();   // optional video
            $table->integer('file_size')->nullable();  // file size in bytes
            $table->string('mime_type')->nullable();   // e.g. audio/mpeg or video/mp4
            

            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
