<?php

use App\Models\Service;
use App\Support\WhatsApp;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('store pages show a whatsapp enquire link when a number is configured', function () {
    $service = Service::query()->where('slug', 'digital-presence-partner')->firstOrFail();
    $url = WhatsApp::enquiryUrl($service);

    expect($url)->toStartWith('https://wa.me/919876543210?text=')
        ->and(urldecode((string) parse_url((string) $url, PHP_URL_QUERY)))->toContain('Digital Presence Partner');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Ask on WhatsApp', false)
        ->assertSee('https://wa.me/919876543210', false);

    $this->get(route('services.show', $service))
        ->assertOk()
        ->assertSee('Ask on WhatsApp')
        ->assertSee(e($url), false);

    $this->get(route('quotes.create', $service))
        ->assertOk()
        ->assertSee('Prefer WhatsApp')
        ->assertSee(e($url), false);
});

test('whatsapp links are hidden when no number is configured', function () {
    config(['services.whatsapp.number' => null]);

    expect(WhatsApp::enquiryUrl())->toBeNull();

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Ask on WhatsApp', false)
        ->assertDontSee('https://wa.me/', false);
});
