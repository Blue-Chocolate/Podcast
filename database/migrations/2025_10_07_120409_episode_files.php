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
       Schema::create('episode_files', function (Blueprint $table) {
    $table->id();
    $table->foreignId('episode_id')->constrained('episodes')->onDelete('cascade');
    $table->string('file_url', 1000);
    $table->string('mime_type', 100)->nullable();
    $table->bigInteger('file_size_bytes')->nullable();
    $table->integer('bitrate_kbps')->nullable();
    $table->string('format', 50)->nullable();
    $table->timestamps();
});

    }

   
    public function down(): void
    {
        //
    }
};
