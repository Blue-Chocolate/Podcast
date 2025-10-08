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
      Schema::create('episode_sponsors', function (Blueprint $table) {
    $table->foreignId('episode_id')->constrained('episodes')->onDelete('cascade');
    $table->foreignId('sponsor_id')->constrained('sponsors')->onDelete('cascade');
    $table->enum('position', ['pre-roll','mid-roll','post-roll','show'])->default('show');
    $table->primary(['episode_id', 'sponsor_id']);
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
