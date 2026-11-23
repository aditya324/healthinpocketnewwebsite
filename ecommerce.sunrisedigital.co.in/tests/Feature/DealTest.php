<?php

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the home page shows the current deals and the sale price', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee("Today's Deals", false)
        ->assertSee('Digital Presence Partner')
        ->assertSee('Marketplace Best Sellers')
        ->assertSee('Growth Marketing Partner')
        ->assertDontSee('Social Media Starter Package');

    $this->get(route('deals'))
        ->assertOk()
        ->assertSee('Performance Growth Partner')
        ->assertSee('Strategic Growth Partner')
        ->assertSee('Ends in');
});

test('see all deals lists every deal including ones past the home strip', function () {
    $source = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();
    $filler = $source->replicate();
    $filler->fill([
        'name' => 'Filler Deal Service',
        'slug' => 'filler-deal-service',
        'is_deal' => true,
        'is_active' => true,
        'sort_order' => 4,
        'price' => 100,
        'compare_price' => 200,
    ]);
    $filler->save();

    $extra = $source->replicate();
    $extra->fill([
        'name' => 'Extra Deal Service',
        'slug' => 'extra-deal-service',
        'is_deal' => true,
        'is_active' => true,
        'sort_order' => 99,
        'price' => 100,
        'compare_price' => 200,
    ]);
    $extra->save();

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Extra Deal Service');

    $this->get(route('deals'))
        ->assertOk()
        ->assertSee("Today's Deals", false)
        ->assertSee('Extra Deal Service')
        ->assertSee('Save 50%')
        ->assertDontSee('Social Media Starter Package')
        ->assertSee(route('cart.store', $extra), false);
});
