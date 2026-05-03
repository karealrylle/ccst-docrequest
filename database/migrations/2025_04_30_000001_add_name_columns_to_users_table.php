<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'first_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('first_name', 100)->nullable()->after('id');
                $table->string('middle_name', 100)->nullable()->after('first_name');
                $table->string('last_name', 100)->nullable()->after('middle_name');
            });

            // Backfill first_name and last_name from existing name column
            DB::table('users')->whereNotNull('name')->get()->each(function ($user) {
                $parts = explode(' ', trim($user->name), 2);
                DB::table('users')->where('id', $user->id)->update([
                    'first_name' => $parts[0] ?? null,
                    'last_name'  => $parts[1] ?? null,
                ]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name']);
        });
    }
};
