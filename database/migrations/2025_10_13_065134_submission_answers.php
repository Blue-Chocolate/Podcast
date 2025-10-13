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
Schema::create('submission_answers', function (Blueprint $table) {
$table->id();
$table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
$table->enum('axis', ['strategy','social','finance','leadership']);
$table->boolean('q1')->nullable();
$table->boolean('q2')->nullable();
$table->boolean('q3')->nullable();
$table->boolean('q4')->nullable();
$table->integer('axis_points')->default(0); // 0..20
$table->text('notes')->nullable();
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('submission_answers');
}
};
