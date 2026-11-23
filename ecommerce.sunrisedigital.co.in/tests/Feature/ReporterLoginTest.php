<?php

use App\Models\Admin;
use App\Models\Reporter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a reporter who signs in on the store login is sent to the reports dashboard', function () {
    $reporter = Reporter::factory()->create();

    $this->post('/login', [
        'email' => $reporter->email,
        'password' => 'password',
    ])->assertRedirect('/reports');

    $this->assertAuthenticatedAs($reporter, 'reporter');
    $this->assertGuest();

    $this->get('/reports')
        ->assertOk()
        ->assertSee($reporter->name);
});

test('a guest who opens the reports area is sent to the reports login', function () {
    $this->get('/reports')->assertRedirect('/reports/login');
});

test('a customer cannot sign in through the reports login', function () {
    $customer = User::factory()->create();

    $this->post('/reports/login', [
        'email' => $customer->email,
        'password' => 'password',
    ])->assertRedirect('/reports/login');

    $this->assertGuest();
    $this->assertGuest('reporter');
});

test('an admin cannot sign in through the reports login', function () {
    $admin = Admin::factory()->create();

    $this->post('/reports/login', [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect('/reports/login');

    $this->assertGuest('reporter');
    $this->assertGuest('admin');
});

test('a reporter can sign in and sign out without a customer session', function () {
    $reporter = Reporter::factory()->create();

    $this->post('/reports/login', [
        'email' => $reporter->email,
        'password' => 'password',
    ])->assertRedirect('/reports');

    $this->assertAuthenticatedAs($reporter, 'reporter');
    $this->assertGuest();

    $this->post('/reports/logout')->assertRedirect('/reports/login');

    $this->assertGuest('reporter');
    $this->get('/reports')->assertRedirect('/reports/login');
});

test('a signed-in customer cannot open the reports area', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get('/reports')
        ->assertRedirect('/reports/login');

    $this->assertAuthenticatedAs($customer);
});

test('a reporter cannot open the admin area', function () {
    $reporter = Reporter::factory()->create();

    $this->actingAs($reporter, 'reporter')
        ->get('/admin')
        ->assertRedirect('/admin/login');

    $this->assertGuest('admin');
    $this->assertAuthenticatedAs($reporter, 'reporter');
});
