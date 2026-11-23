<?php

use App\Models\Admin;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('an admin can save onboarding questions for a service', function () {
    $admin = Admin::factory()->create();
    $service = Service::query()->where('slug', 'comprehensive-technical-seo')->firstOrFail();

    $this->actingAs($admin, 'admin')->put(route('admin.services.update', $service), [
        'name' => $service->name,
        'category_id' => $service->category_id,
        'short_description' => $service->short_description,
        'points' => [
            ['title' => 'What is included', 'body' => 'The full offer for this service.'],
        ],
        'price' => $service->price,
        'billing_type' => $service->billing_type,
        'questions' => [
            ['prompt' => 'What is the site URL?', 'help' => 'The domain we should review.', 'required' => '1'],
            ['prompt' => '', 'help' => '', 'required' => '0'],
            ['prompt' => 'Anything else?', 'help' => '', 'required' => '0'],
        ],
    ])->assertRedirect('/admin/services')->assertSessionHasNoErrors();

    $questions = $service->fresh()->questions();

    expect($questions)->toHaveCount(2)
        ->and($questions[0]['prompt'])->toBe('What is the site URL?')
        ->and($questions[0]['help'])->toBe('The domain we should review.')
        ->and($questions[0]['required'])->toBeTrue()
        ->and($questions[1]['prompt'])->toBe('Anything else?')
        ->and($questions[1]['required'])->toBeFalse();
});

test('each social media package has its own onboarding questions', function () {
    $prompts = fn (string $slug) => collect(Service::query()->where('slug', $slug)->firstOrFail()->questions())->pluck('prompt');

    $digital = $prompts('digital-presence-partner');
    $growth = $prompts('growth-marketing-partner');
    $performance = $prompts('performance-growth-partner');
    $strategic = $prompts('strategic-growth-partner');

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get(route('admin.services.edit', Service::query()->where('slug', 'digital-presence-partner')->firstOrFail()))
        ->assertOk()
        ->assertSee('What is your business name, industry and primary product or service?')
        ->assertSee('Onboarding questions');

    expect($digital)->toContain('What is your business name, industry and primary product or service?')
        ->and($digital)->toContain('Are there any specific competitors or reference brands whose social media presence you would like us to consider?')
        ->and($growth)->not->toContain('What is your business name, industry and primary product or service?')
        ->and($growth)->toContain('What are your primary business growth and marketing objectives?')
        ->and($performance)->toContain('Which keywords or search terms would you like your business to rank for on Google?')
        ->and($strategic)->toContain('What key business metrics would you like to monitor through a centralised performance dashboard?');
});

test('paying for a service with questions opens onboarding', function () {
    $user = User::factory()->create();
    $service = Service::query()->where('slug', 'comprehensive-technical-seo')->firstOrFail();
    $details = is_array($service->details) ? $service->details : [];
    $details['questions'] = [
        ['prompt' => 'What is the site URL?', 'help' => 'The domain we should review.', 'required' => true],
        ['prompt' => 'Anything else?', 'help' => null, 'required' => false],
    ];
    $service->update(['details' => $details]);

    $this->actingAs($user)
        ->post(route('checkout.buy', $service))
        ->assertRedirect(route('checkout.create'));

    payForCheckout([
        'name' => $user->name,
        'email' => $user->email,
    ])->assertRedirect(route('orders.onboarding', $order = Order::query()->firstOrFail()))
        ->assertSessionHas('status', 'Order placed.');

    $service->update(['details' => []]);

    $item = $order->items()->firstOrFail();

    expect($item->onboardingQuestions()[0]['prompt'])->toBe('What is the site URL?');

    $this->get(route('orders.onboarding', $order))
        ->assertOk()
        ->assertSee('What is the site URL?')
        ->assertSee('The domain we should review.')
        ->assertSee('Anything else?')
        ->assertSee('Answer with microphone')
        ->assertSee('data-voice-dot', false);

    $this->get(route('profile.orders'))
        ->assertOk()
        ->assertSee('Your onboarding process is still pending.')
        ->assertSee('Finish onboarding');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Your onboarding process is still pending.')
        ->assertDontSee('Open dashboard');

    $this->get(route('dashboard'))
        ->assertRedirect(route('orders.onboarding', $order));

    $this->post(route('orders.onboarding.update', $order), [
        'items' => [
            $item->id => ['answers' => [1 => 'A later note']],
        ],
    ])->assertSessionHasErrors('items.'.$item->id.'.answers.0');

    expect($item->fresh()->needsOnboarding())->toBeTrue();

    $this->post(route('orders.onboarding.update', $order), [
        'items' => [
            $item->id => [
                'answers' => [
                    0 => 'sunrise.example',
                    1 => '',
                ],
            ],
        ],
    ])->assertRedirect(route('dashboard'));

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Your onboarding process is complete')
        ->assertSee('You can now access your dashboard.')
        ->assertSee('data-onboarding-complete', false)
        ->assertSee($service->name)
        ->assertSee('Dashboard');

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('data-onboarding-complete', false);

    $this->get(route('orders.show', $order))
        ->assertOk()
        ->assertSee('sunrise.example')
        ->assertSee('Not answered')
        ->assertSee('Open dashboard')
        ->assertDontSee('Your onboarding process is still pending.')
        ->assertDontSee('Finish onboarding');

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get(route('admin.orders.show', $order))
        ->assertOk()
        ->assertSee('What is the site URL?')
        ->assertSee('sunrise.example');
});

test('a microphone recording can answer a required question', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $service = Service::query()->where('slug', 'comprehensive-technical-seo')->firstOrFail();
    $details = is_array($service->details) ? $service->details : [];
    $details['questions'] = [
        ['prompt' => 'What is the site URL?', 'help' => null, 'required' => true],
    ];
    $service->update(['details' => $details]);

    $this->actingAs($user)->post(route('checkout.buy', $service));

    payForCheckout([
        'name' => $user->name,
        'email' => $user->email,
    ]);

    $order = Order::query()->firstOrFail();
    $item = $order->items()->firstOrFail();

    $this->post(route('orders.onboarding.audio.store', [$order, $item, 0]), [
        'audio' => UploadedFile::fake()->create('answer.webm', 40, 'audio/webm'),
    ])->assertOk();

    $saved = $item->fresh();

    expect($saved->onboardingQuestions()[0]['audio'])->toStartWith('onboarding/');
    Storage::disk('local')->assertExists($saved->onboardingQuestions()[0]['audio']);

    $this->post(route('orders.onboarding.update', $order), [
        'items' => [
            $item->id => ['answers' => [0 => '']],
        ],
    ])->assertRedirect(route('dashboard'));

    $this->get(route('orders.show', $order))
        ->assertOk()
        ->assertSee(route('orders.onboarding.audio', [$order, $item, 0]), false);

    $this->get(route('orders.onboarding.audio', [$order, $item, 0]))
        ->assertOk();

    $this->actingAs(User::factory()->create())
        ->get(route('orders.onboarding.audio', [$order, $item, 0]))
        ->assertNotFound();

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get(route('orders.onboarding.audio', [$order, $item, 0]))
        ->assertOk();
});

test('leaving onboarding keeps the answers for later', function () {
    $user = User::factory()->create();
    $service = Service::query()->where('slug', 'comprehensive-technical-seo')->firstOrFail();
    $details = is_array($service->details) ? $service->details : [];
    $details['questions'] = [
        ['prompt' => 'What is the site URL?', 'help' => null, 'required' => true],
        ['prompt' => 'Anything else?', 'help' => null, 'required' => false],
    ];
    $service->update(['details' => $details]);

    $this->actingAs($user)->post(route('checkout.buy', $service));

    payForCheckout([
        'name' => $user->name,
        'email' => $user->email,
    ]);

    $order = Order::query()->firstOrFail();
    $item = $order->items()->firstOrFail();

    $this->postJson(route('orders.onboarding.draft', $order), [
        'items' => [
            $item->id => [
                'answers' => [
                    0 => 'sunrise.example',
                ],
            ],
        ],
    ])->assertOk()->assertJson(['saved' => true]);

    $saved = $item->fresh();

    expect($saved->needsOnboarding())->toBeTrue()
        ->and($saved->onboardingQuestions()[0]['answer'])->toBe('sunrise.example');

    $this->get(route('orders.onboarding', $order))
        ->assertOk()
        ->assertSee('sunrise.example')
        ->assertSee('continue from the order');

    $this->get(route('orders.show', $order))
        ->assertOk()
        ->assertSee('Finish onboarding')
        ->assertSee(route('orders.onboarding', $order), false);
});

test('a customer cannot buy a service they already purchased', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $service = Service::query()->where('slug', 'comprehensive-technical-seo')->firstOrFail();

    $this->actingAs($user)->post(route('checkout.buy', $service));

    payForCheckout([
        'name' => $user->name,
        'email' => $user->email,
    ]);

    $order = Order::query()->firstOrFail();

    $this->actingAs($user)
        ->from(route('services.show', $service))
        ->post(route('cart.store', $service))
        ->assertRedirect(route('services.show', $service))
        ->assertSessionHasErrors('service');

    $this->post(route('checkout.buy', $service))
        ->assertSessionHasErrors('service');

    $this->get(route('services.show', $service))
        ->assertOk()
        ->assertSee('You already bought this.')
        ->assertSee('View your order')
        ->assertDontSee('Add to Cart')
        ->assertDontSee('Buy Now');

    $this->withSession([
        'cart' => [
            $service->id.'-0' => [
                'service_id' => $service->id,
                'months' => null,
                'quantity' => 1,
            ],
        ],
    ])->get(route('cart.index'))
        ->assertRedirect(route('cart.index'));

    expect(session('cart'))->toBe([]);

    $this->actingAs($other)
        ->post(route('cart.store', $service))
        ->assertRedirect(route('cart.index'));

    $order->update(['status' => 'refunded']);

    $this->actingAs($user)
        ->post(route('cart.store', $service))
        ->assertRedirect(route('cart.index'));
});

test('paying for a service without questions opens the order', function () {
    $user = User::factory()->create();
    $service = Service::query()->where('slug', 'comprehensive-technical-seo')->firstOrFail();

    $this->actingAs($user)->post(route('checkout.buy', $service));

    payForCheckout([
        'name' => $user->name,
        'email' => $user->email,
    ])->assertRedirect(route('orders.show', Order::query()->firstOrFail()));
});

test('another customer cannot open onboarding', function () {
    $buyer = User::factory()->create();
    $service = Service::query()->where('slug', 'comprehensive-technical-seo')->firstOrFail();
    $details = is_array($service->details) ? $service->details : [];
    $details['questions'] = [
        ['prompt' => 'What is the site URL?', 'help' => null, 'required' => true],
    ];
    $service->update(['details' => $details]);

    $this->actingAs($buyer)->post(route('checkout.buy', $service));

    payForCheckout([
        'name' => $buyer->name,
        'email' => $buyer->email,
    ]);

    $order = Order::query()->firstOrFail();

    $this->actingAs(User::factory()->create())
        ->get(route('orders.onboarding', $order))
        ->assertNotFound();

    $this->post(route('orders.onboarding.update', $order), [
        'items' => [
            $order->items()->firstOrFail()->id => ['answers' => [0 => 'Not your order']],
        ],
    ])->assertNotFound();

    expect($order->items()->firstOrFail()->needsOnboarding())->toBeTrue();
});
