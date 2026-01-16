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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_author_id')->default(1);
            $table->unsignedBigInteger('blog_image_id')->default(1);
            $table->unsignedBigInteger('blog_category_id')->default(1);
            //
            $table->dateTime('blog_date')->nullable();
            $table->string('title', 100)->nullable();
            $table->string('slug', 200)->nullable();
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            $table->integer('reading_time')->nullable()->default(3);
            //
            $table->boolean('audio_on')->nullable()->default(false);
            $table->string('audio_url', 100)->nullable();
            $table->integer('audio_duration')->nullable()->default(0);
            //
            $table->boolean('markdown_on')->nullable()->default(false);
            //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
