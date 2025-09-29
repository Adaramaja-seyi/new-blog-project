<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Set email_verified_at for all users who don't have it
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down()
    {
        // Optionally revert (set email_verified_at to null for all users)
        // DB::table('users')->update(['email_verified_at' => null]);
    }
};
