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
       Schema::create('plays', function (Blueprint $table) {
    $table->id();
    $table->foreignId('episode_id')->constrained('episodes')->onDelete('cascade');
    $table->dateTime('played_at')->default(now());
    $table->binary('ip_address')->nullable();
    $table->string('user_agent', 1000)->nullable();
    $table->string('referrer', 1000)->nullable();
    $table->integer('duration_listened_seconds')->nullable();
    $table->timestamps();

    $table->index('episode_id');
    $table->index('played_at');
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
