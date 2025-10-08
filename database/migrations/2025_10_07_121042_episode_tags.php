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
        Schema::create('episode_tags', function (Blueprint $table) {
    $table->foreignId('episode_id')->constrained('episodes')->onDelete('cascade');
    $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
    $table->primary(['episode_id', 'tag_id']);
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
