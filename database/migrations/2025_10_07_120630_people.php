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
       Schema::create('people', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique()->nullable();
    $table->enum('role', ['host','producer','guest','engineer','other'])->default('guest');
    $table->text('bio')->nullable();
    $table->string('avatar_url', 500)->nullable();
    $table->string('website', 500)->nullable();
    $table->json('social_json')->nullable();
    $table->timestamps();

    $table->index('name');
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
