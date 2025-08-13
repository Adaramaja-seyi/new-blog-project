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
        Schema::table('posts', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('title');
            $table->enum('status', ['draft', 'published', 'pending'])->default('draft')->after('is_published');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->string('meta_title')->nullable()->after('content');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->integer('views_count')->default(0)->after('featured_image');
            $table->timestamp('published_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'excerpt',
                'status',
                'category_id',
                'meta_title',
                'meta_description',
                'views_count',
                'published_at'
            ]);
        });
    }
};
