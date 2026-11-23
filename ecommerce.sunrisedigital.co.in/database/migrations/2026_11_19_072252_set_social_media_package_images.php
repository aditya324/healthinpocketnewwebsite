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
        foreach ($this->slugs() as $slug) {
            DB::table('services')->where('slug', $slug)->update([
                'image' => $slug.'.jpg',
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('services')->whereIn('slug', $this->slugs())->update([
            'image' => 'catalog-social.jpg',
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
