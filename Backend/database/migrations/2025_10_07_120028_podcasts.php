<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('podcasts', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 150)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('language', 10)->default('ar');
            $table->string('website_url', 500)->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->string('rss_url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('podcasts');
    }
};
