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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('phone')->nullable()->after('username');
            $table->string('website')->nullable()->after('phone');
            $table->string('location')->nullable()->after('website');
            $table->enum('role', ['user', 'author', 'admin'])->default('user')->after('location');
            $table->boolean('email_notifications')->default(true)->after('role');
            $table->boolean('marketing_emails')->default(false)->after('email_notifications');
            $table->timestamp('last_login_at')->nullable()->after('marketing_emails');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'username',
                'phone',
                'website',
                'location',
                'role',
                'email_notifications',
                'marketing_emails',
                'last_login_at'
            ]);
        });
    }
};
