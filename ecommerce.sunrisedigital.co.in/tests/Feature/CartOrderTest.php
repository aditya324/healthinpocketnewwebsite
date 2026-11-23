<?php

use App\Models\Admin;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Support\Bill;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a visitor can add a monthly service to the cart', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();

    $this->post(route('cart.store', $service))
        ->assertRedirect(route('cart.index'));

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('Instagram Marketing')
        ->assertSee('Billed monthly')
        ->assertSee('Due today')
        ->assertSee($service->money((float) $service->price))
        ->assertDontSee('3 Months')
        ->assertDontSee('Renews monthly for');
});

test('the cart summary separates a one-time payment from a subscription', function () {
    $monthly = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();
    $once = Service::query()->where('slug', 'comprehensive-technical-seo')->firstOrFail();

    $this->post(route('cart.store', $monthly));
    $this->post(route('cart.store', $once));

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('Pay once')
        ->assertSee('Subscription')
        ->assertSee('Due today')
        ->assertSee($once->name)
        ->assertSee($monthly->name)
        ->assertSee('Billed monthly')
        ->assertDontSee('Renews monthly for')
        ->assertSee($monthly->money((float) $monthly->price))
        ->assertSee($once->money((float) $once->price));
});

test('buy now places an order for the monthly price and leaves the cart alone', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();
    $other = Service::query()->where('slug', 'high-conversion-google-ads')->firstOrFail();
    $price = (float) $service->price;

    $this->actingAs(User::factory()->create());

    $this->post(route('cart.store', $other))
        ->assertRedirect(route('cart.index'));

    $this->post(route('checkout.buy', $service))
        ->assertRedirect(route('checkout.create'));

    $this->get(route('checkout.create'))
        ->assertOk()
        ->assertSee('Instagram Marketing')
        ->assertSee('renews monthly')
        ->assertDontSee('3 Months')
        ->assertDontSee('High-Conversion Google Ads Management');

    payForCheckout()->assertRedirect();

    $order = Order::query()->firstOrFail();

    expect($order->name)->toBe('Asha Menon')
        ->and($order->email)->toBe('asha@example.com')
        ->and($order->status)->toBe('placed')
        ->and((float) $order->gst)->toBe((float) Bill::gstOn($price))
        ->and((float) $order->total)->toBe((float) ($price + Bill::gstOn($price)));

    $this->get(route('orders.show', $order))
        ->assertOk()
        ->assertSee('Order placed.')
        ->assertSee($order->number)
        ->assertSee('renews monthly')
        ->assertDontSee('3 Months')
        ->assertSee($service->money($price));

    $this->get(route('cart.index'))
        ->assertSee('High-Conversion Google Ads Management');
});

test('a signed-in customer is attached to the order', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('checkout.buy', $service), ['months' => 1]);

    payForCheckout([
        'name' => $user->name,
        'email' => $user->email,
    ]);

    expect(Order::query()->firstOrFail()->user_id)->toBe($user->id);
});

test('a posted month count does not change the monthly price', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();

    $this->post(route('cart.store', $service), [
        'months' => 2,
    ])->assertRedirect(route('cart.index'));

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('Instagram Marketing')
        ->assertSee($service->money((float) $service->price))
        ->assertDontSee('2 Months');
});

test('a hidden service cannot be added or bought', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();
    $service->update(['is_active' => false]);

    $this->post(route('cart.store', $service), ['months' => 1])->assertNotFound();
    $this->post(route('checkout.buy', $service), ['months' => 1])->assertNotFound();
});

test('quantity and save for later stay on the cart page', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();
    $offer = $service->offer(1);

    $this->post(route('cart.store', $service), ['months' => 1]);
    $key = $service->id.'-0';

    $this->post(route('cart.quantity'), [
        'key' => $key,
        'quantity' => 2,
    ])->assertRedirect(route('cart.index'));

    $this->get(route('cart.index'))
        ->assertSee('Order Summary')
        ->assertSee('Proceed to Checkout')
        ->assertSee('Save for later')
        ->assertSee($service->money($offer['price'] * 2));

    $this->post(route('cart.save'), ['key' => $key])
        ->assertRedirect(route('cart.index'));

    $this->get(route('cart.index'))
        ->assertSee('Saved for Later')
        ->assertSee('Move to Cart')
        ->assertDontSee('Proceed to Checkout');

    $this->post(route('cart.move'), ['key' => $key])
        ->assertRedirect(route('cart.index'));

    $this->get(route('cart.index'))->assertSee('Proceed to Checkout');
});

test('checkout without a cart returns to the cart', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('checkout.create'))->assertRedirect(route('cart.index'));
    $this->post(route('checkout.store'), [
        ...checkoutCustomer(),
    ])->assertRedirect(route('cart.index'));

    expect(Order::query()->count())->toBe(0);
});

test('an admin can see a placed order', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();

    $this->actingAs(User::factory()->create())
        ->post(route('checkout.buy', $service), ['months' => 1]);
    payForCheckout();

    $order = Order::query()->firstOrFail();

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get(route('admin.orders.index'))
        ->assertOk()
        ->assertSee($order->number)
        ->assertSee('Asha Menon')
        ->assertSee($order->money());

    $this->get(route('admin.orders.show', $order))
        ->assertOk()
        ->assertSee('Instagram Marketing')
        ->assertSee('30 Days Delivery')
        ->assertDontSee('1 Month');
});

test('the filter page can add the service shown on the card', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();

    $this->get(route('categories.show', $service->category))
        ->assertOk()
        ->assertSee(route('cart.store', $service), false);

    $this->post(route('cart.store', $service), [
        'months' => 1,
    ])->assertRedirect(route('cart.index'));

    $this->get(route('cart.index'))
        ->assertSee('Instagram Marketing')
        ->assertSee('Billed monthly')
        ->assertDontSee('1 Month');
});

test('an admin can see the service a signed-in customer added', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();
    $user = User::factory()->create([
        'name' => 'Asha Menon',
        'email' => 'asha@example.com',
    ]);

    $this->actingAs($user)->post(route('cart.store', $service), [
        'months' => 1,
    ]);

    expect(CartItem::query()->where('user_id', $user->id)->count())->toBe(1);

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get(route('admin.carts.index'))
        ->assertOk()
        ->assertSee('Asha Menon')
        ->assertSee('asha@example.com')
        ->assertSee('Instagram Marketing')
        ->assertSee('30 Days Delivery')
        ->assertDontSee('1 Month');

    $this->actingAs($user)->post(route('cart.destroy'), [
        'key' => $service->id.'-0',
    ]);

    expect(CartItem::query()->count())->toBe(0);
});

test('a guest cart stays off the admin list', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();

    $this->post(route('cart.store', $service), ['months' => 1]);

    expect(CartItem::query()->count())->toBe(0);

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get(route('admin.carts.index'))
        ->assertOk()
        ->assertSee('No carts yet.');
});

test('a guest cannot open the admin carts', function () {
    $this->get(route('admin.carts.index'))
        ->assertRedirect(route('admin.login'));
});

test('a guest cannot open the admin orders', function () {
    $this->get(route('admin.orders.index'))
        ->assertRedirect(route('admin.login'));
});

test('a guest is sent to login before they can buy or check out', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();

    $this->post(route('cart.store', $service), ['months' => 1]);

    $this->get(route('checkout.create'))
        ->assertRedirect(route('login'));

    $this->post(route('checkout.store'), [
        ...checkoutCustomer(),
    ])->assertRedirect(route('login'));

    $this->post(route('checkout.buy', $service), ['months' => 1])
        ->assertRedirect(route('login'));

    expect(session('url.intended'))->toBe(route('checkout.create'))
        ->and(Order::query()->count())->toBe(0);
});

test('buy now continues at checkout after the guest signs in', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();
    $user = User::factory()->create();

    $this->post(route('checkout.buy', $service), ['months' => 1])
        ->assertRedirect(route('login'));

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('checkout.create'));

    $this->get(route('checkout.create'))
        ->assertOk()
        ->assertSee('Instagram Marketing');
});

test('a customer cannot open another customer order', function () {
    $service = Service::query()->where('slug', 'instagram-marketing')->firstOrFail();
    $buyer = User::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($buyer)
        ->post(route('checkout.buy', $service), ['months' => 1]);

    payForCheckout([
        'name' => $buyer->name,
        'email' => $buyer->email,
    ]);

    $order = Order::query()->firstOrFail();

    $this->actingAs($other)
        ->get(route('orders.show', $order))
        ->assertNotFound();
});
