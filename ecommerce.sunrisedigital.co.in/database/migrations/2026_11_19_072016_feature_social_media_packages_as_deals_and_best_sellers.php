<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('services')
            ->whereIn('slug', $this->slugs())
            ->update([
                'is_deal' => true,
                'is_best_seller' => true,
                'deal_ends_at' => now()->addDays(7),
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('services')
            ->whereIn('slug', $this->slugs())
            ->update([
                'is_deal' => false,
                'is_best_seller' => false,
                'deal_ends_at' => null,
                'updated_at' => now(),
            ]);
    }

    /**
     * @return list<string>
     */
    private function slugs(): array
    {
        return [
            'digital-presence-partner',
            'growth-marketing-partner',
            'performance-growth-partner',
            'strategic-growth-partner',
        ];
    }
};
