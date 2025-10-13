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
Schema::create('reviews', function (Blueprint $table) {
$table->id();
$table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
$table->foreignId('judge_id')->constrained('judges')->cascadeOnDelete();
$table->json('answers'); // store q1..q4 per axis or a map
$table->integer('total_points')->default(0);
$table->text('comment')->nullable();
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('reviews');
}
};
