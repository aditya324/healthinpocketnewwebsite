<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reporter_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->date('period_start');
            $table->date('period_end');
            $table->date('previous_start');
            $table->date('previous_end');
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->json('facebook')->nullable();
            $table->json('instagram')->nullable();
            $table->json('youtube')->nullable();
            $table->json('narrative')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_reports');
    }
};
