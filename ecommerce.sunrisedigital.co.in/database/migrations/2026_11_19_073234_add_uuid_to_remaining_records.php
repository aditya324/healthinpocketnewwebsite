<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->tables() as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });

            DB::table($table)->orderBy('id')->lazyById()->each(function (object $record) use ($table): void {
                DB::table($table)->where('id', $record->id)->update([
                    'uuid' => (string) Str::uuid7(),
                ]);
            });

            Schema::table($table, function (Blueprint $table) {
                $table->uuid('uuid')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables() as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }

    /**
     * @return list<string>
     */
    private function tables(): array
    {
        return [
            'users',
            'admins',
            'categories',
            'plans',
            'plan_items',
            'quotes',
            'orders',
            'order_items',
            'cart_items',
            'coupons',
            'coupon_uses',
            'wishlist_items',
            'businesses',
            'reviews',
            'subscriptions',
            'cart_drops',
            'support_messages',
        ];
    }
};
