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
Schema::create('submissions', function (Blueprint $table) {
$table->id();
$table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
$table->enum('status', ['draft','submitted','under_review','shortlisted','winner','rejected'])->default('draft');
$table->decimal('total_score', 6, 2)->nullable();
$table->timestamp('submitted_at')->nullable();
$table->timestamp('announced_at')->nullable();
$table->json('meta')->nullable(); // flexible for extra fields
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('submissions');
}

};
