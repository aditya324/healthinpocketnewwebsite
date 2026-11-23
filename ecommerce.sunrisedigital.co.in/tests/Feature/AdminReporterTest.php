<?php

use App\Models\Admin;
use App\Models\Reporter;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a guest is sent to the admin login before managing reporters', function () {
    $this->get(route('admin.reporters.index'))
        ->assertRedirect(route('admin.login'));
});

test('a customer cannot manage reporters', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.reporters.index'))
        ->assertRedirect(route('admin.login'));
});

test('an admin can add a reporter who then signs in on the reports dashboard', function () {
    $this->actingAs(Admin::factory()->create(), 'admin')
        ->post(route('admin.reporters.store'), [
            'name' => 'Meera Shah',
            'email' => 'meera@example.com',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
        ])
        ->assertRedirect(route('admin.reporters.index'))
        ->assertSessionHasNoErrors();

    $this->post('/admin/logout');

    $this->post('/reports/login', [
        'email' => 'meera@example.com',
        'password' => 'secret-pass',
    ])->assertRedirect('/reports');

    $this->assertAuthenticatedAs(Reporter::query()->where('email', 'meera@example.com')->first(), 'reporter');
    $this->assertGuest('admin');
});

test('an admin can rename a reporter without changing the password', function () {
    $admin = Admin::factory()->create();
    $reporter = Reporter::factory()->create([
        'name' => 'Meera Shah',
        'email' => 'meera@example.com',
    ]);

    $this->actingAs($admin, 'admin')
        ->put(route('admin.reporters.update', $reporter), [
            'name' => 'Meera N. Shah',
            'email' => 'meera@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('admin.reporters.index'))
        ->assertSessionHasNoErrors();

    $this->post('/admin/logout');

    $this->post('/reports/login', [
        'email' => 'meera@example.com',
        'password' => 'password',
    ])->assertRedirect('/reports');

    expect($reporter->refresh()->name)->toBe('Meera N. Shah');
});

test('removing a reporter keeps the reports they already filed', function () {
    $report = WeeklyReport::factory()->create();
    $reporter = $report->reporter;

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->delete(route('admin.reporters.destroy', $reporter))
        ->assertRedirect(route('admin.reporters.index'));

    $this->assertModelMissing($reporter);
    expect($report->refresh()->reporter_id)->toBeNull();
    $this->assertModelExists($report);
});
