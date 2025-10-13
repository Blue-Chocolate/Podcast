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
Schema::create('attachments', function (Blueprint $table) {
$table->id();
$table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
$table->string('axis')->nullable();
$table->string('original_name');
$table->string('path');
$table->string('mime_type')->nullable();
$table->bigInteger('size')->nullable();
$table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
$table->timestamps();
});
}


public function down(): void
{
Schema::dropIfExists('attachments');
}
};
