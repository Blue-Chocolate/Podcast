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
        Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('episode_id')->constrained('episodes')->onDelete('cascade');
    $table->string('user_name')->nullable();
    $table->string('user_email')->nullable();
    $table->text('content');
    $table->boolean('approved')->default(false);
    $table->timestamps();
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
