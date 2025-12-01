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
       Schema::create('playlist_episodes', function (Blueprint $table) {
    $table->foreignId('playlist_id')->constrained('playlists')->onDelete('cascade');
    $table->foreignId('episode_id')->constrained('episodes')->onDelete('cascade');
    $table->integer('ord')->default(0);
    $table->primary(['playlist_id', 'episode_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
