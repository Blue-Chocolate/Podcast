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
       Schema::create('episode_categories', function (Blueprint $table) {
    $table->foreignId('episode_id')->constrained('episodes')->onDelete('cascade');
    $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
    $table->primary(['episode_id', 'category_id']);
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
