<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('weekly_reports', 'kind')) {
            Schema::table('weekly_reports', function (Blueprint $table) {
                $table->string('kind')->default('weekly')->after('title');
            });
        }

        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->unique(['user_id', 'kind', 'period_start']);
        });

        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->unique(['user_id', 'period_start']);
        });

        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'kind', 'period_start']);
            $table->dropColumn('kind');
        });
    }
};
