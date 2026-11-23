<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Concerns\HasPublicUuid;
use App\Models\Service;
use App\Models\SupportMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('every model keeps a public uuid beside its numeric id', function () {
    $models = collect(File::files(app_path('Models')))
        ->map(fn (SplFileInfo $file): string => 'App\\Models\\'.$file->getBasename('.php'));

    expect($models->all())->not->toBeEmpty();

    foreach ($models as $model) {
        expect(in_array(HasPublicUuid::class, class_uses_recursive($model), true))->toBeTrue();
    }
});

test('existing records and new accounts each have a uuid', function () {
    $admin = Admin::factory()->create();
    $message = SupportMessage::query()->create([
        'name' => 'Asha',
        'email' => 'asha@sunrise.test',
        'body' => 'Hello',
    ]);
    $service = Service::query()->where('slug', 'digital-presence-partner')->firstOrFail();
    $category = Category::query()->where('slug', 'social-media')->firstOrFail();

    expect(Str::isUuid($admin->uuid))->toBeTrue()
        ->and(Str::isUuid($message->uuid))->toBeTrue()
        ->and(Str::isUuid($service->uuid))->toBeTrue()
        ->and(Str::isUuid($category->uuid))->toBeTrue()
        ->and($service->uuid)->not->toBe($category->uuid);

    foreach ([
        'users',
        'admins',
        'categories',
        'services',
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
    ] as $table) {
        expect(DB::table($table)->whereNull('uuid')->doesntExist())->toBeTrue();
    }
});
