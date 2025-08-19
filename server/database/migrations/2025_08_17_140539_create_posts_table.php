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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('featured_image')->nullable();
            $table->text('excerpt')->nullable();
            $table->enum('is_published', ['yes', 'no'])->default('no');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('meta_title')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            //foreign key constraints
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
