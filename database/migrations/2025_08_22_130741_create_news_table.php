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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('category_id');
            $table->integer('views')->default(0);
            $table->string('slug')->unique();
            $table->boolean('is_show')->default(true);
            $table->boolean('is_pin')->default(false);
            $table->string('thumbnail')->nullable();
            $table->string('short_description')->nullable();
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('news_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
