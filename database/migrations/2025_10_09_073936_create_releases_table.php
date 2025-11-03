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
    Schema::create('releases', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->integer('views_count');
        $table->string('file_path');
        $table->string('excel_path')->nullable();
        $table->string('powerbi_path')->nullable();
        $table->text('description')->nullable();
        $table->json('images')->nullable(); // ← بدل string image
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
