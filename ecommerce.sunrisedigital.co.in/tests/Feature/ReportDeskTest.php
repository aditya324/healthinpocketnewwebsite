<?php

use App\Mail\ReportPublishedMail;
use App\Models\Admin;
use App\Models\Reporter;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('the client sees the biggest rise and drop', function () {
    $customer = User::factory()->create();
    WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'social',
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
        'previous_start' => '2026-08-01',
        'previous_end' => '2026-08-31',
        'body' => [
            'instagram' => ['metrics' => [['label' => 'Views', 'value' => '100']]],
            'facebook' => ['metrics' => [['label' => 'Views', 'value' => '200']]],
        ],
    ]);
    $october = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'social',
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-31',
        'previous_start' => '2026-09-01',
        'previous_end' => '2026-09-30',
        'body' => [
            'instagram' => ['metrics' => [['label' => 'Views', 'value' => '150']]],
            'facebook' => ['metrics' => [['label' => 'Views', 'value' => '100']]],
        ],
    ]);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $october))
        ->assertSee('Instagram is up 50%')
        ->assertSee('Facebook is down 50%');
});

test('publishing a report emails the client a link', function () {
    Mail::fake();

    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();

    $this->actingAs($reporter, 'reporter')
        ->post(route('reports.weekly-reports.store', $customer), [
            'pdf' => new UploadedFile(base_path('tests/Fixtures/nano-weekly-report.pdf'), 'weekly.pdf', 'application/pdf', null, true),
            'intent' => 'publish',
            'kind' => 'weekly',
        ])
        ->assertSessionHasNoErrors();

    Mail::assertSent(ReportPublishedMail::class, function (ReportPublishedMail $mail) use ($customer): bool {
        return $mail->hasTo($customer->email);
    });
});

test('a client can flag a number and the reporter can read it', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $other = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'facebook' => ['current' => ['views' => '862'], 'previous' => ['views' => '800']],
    ]);

    $this->actingAs($other, 'web')
        ->post(route('dashboard.reports.flags.store', $report), [
            'path' => 'facebook.current.views',
            'message' => 'This looks wrong',
        ])
        ->assertNotFound();

    $this->actingAs($customer, 'web')
        ->post(route('dashboard.reports.flags.store', $report), [
            'path' => 'facebook.current.views',
            'message' => 'This looks wrong',
        ])
        ->assertRedirect();

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.weekly-reports.show', $report))
        ->assertSee('This looks wrong')
        ->assertSee('Notes from the client');
});

test('the client can download a summary of the on-screen report', function () {
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'facebook' => ['current' => ['views' => '862'], 'previous' => ['views' => '800']],
    ]);

    $this->get(route('weekly-reports.summary', $report))->assertNotFound();

    $this->actingAs($customer, 'web')
        ->get(route('weekly-reports.summary', $report))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('a reporter sees who still needs a report', function () {
    $reporter = Reporter::factory()->create();
    $waiting = User::factory()->create(['name' => 'Cedar Clinic']);

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.home'))
        ->assertSee('Still to file')
        ->assertSee('Cedar Clinic');

    WeeklyReport::factory()->published()->create([
        'user_id' => $waiting->id,
        'kind' => 'weekly',
        'period_start' => now()->startOfWeek(Carbon::SUNDAY)->toDateString(),
        'period_end' => now()->startOfWeek(Carbon::SUNDAY)->addDays(6)->toDateString(),
        'previous_start' => now()->startOfWeek(Carbon::SUNDAY)->subWeek()->toDateString(),
        'previous_end' => now()->startOfWeek(Carbon::SUNDAY)->subDay()->toDateString(),
    ]);

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.home'))
        ->assertSee('Everyone is filed.');
});

test('correcting a number keeps the old value', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'facebook' => ['current' => ['views' => '862'], 'previous' => ['views' => '800']],
    ]);
    $report->pdf_path = 'weekly-reports/sample/source.pdf';
    $report->save();

    $this->actingAs($reporter, 'reporter')
        ->put(route('reports.weekly-reports.update', $report), [
            'intent' => 'publish',
            'figures' => [
                ['path' => 'facebook.current.views', 'value' => '900'],
            ],
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.weekly-reports.show', $report))
        ->assertSee('Number changes')
        ->assertSee('862')
        ->assertSee('900')
        ->assertSee($reporter->name);
});

test('a reporter only sees clients assigned to them', function () {
    $owner = Reporter::factory()->create();
    $other = Reporter::factory()->create();
    $customer = User::factory()->create(['name' => 'Assigned Clinic', 'assigned_reporter_id' => $owner->id]);
    User::factory()->create(['name' => 'Open Clinic']);

    $this->actingAs($owner, 'reporter')
        ->get(route('reports.home'))
        ->assertSee('Assigned Clinic')
        ->assertSee('Open Clinic');

    $this->actingAs($other, 'reporter')
        ->get(route('reports.clients.show', $customer))
        ->assertNotFound();

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->put(route('admin.customers.reporter', $customer), [
            'assigned_reporter_id' => $other->id,
        ])
        ->assertRedirect();

    expect((int) $customer->refresh()->assigned_reporter_id)->toBe($other->id);
});

test('a private note stays off the client page', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
    ]);

    $this->actingAs($reporter, 'reporter')
        ->put(route('reports.weekly-reports.note', $report), [
            'staff_note' => 'Waiting on October creatives.',
        ])
        ->assertRedirect();

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.weekly-reports.show', $report))
        ->assertSee('Waiting on October creatives.');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $report))
        ->assertDontSee('Waiting on October creatives.');
});

test('the admin overview counts reports', function () {
    $customer = User::factory()->create();
    User::factory()->create();
    WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'published_at' => now(),
    ]);
    WeeklyReport::factory()->create([
        'user_id' => $customer->id,
        'kind' => 'social',
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-31',
        'previous_start' => '2026-09-01',
        'previous_end' => '2026-09-30',
        'status' => 'draft',
    ]);

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get(route('admin.home'))
        ->assertSee('Published this month')
        ->assertSee('Drafts waiting')
        ->assertSee('Clients with nothing filed');
});

test('a deleted report can be restored', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'title' => 'October social restore',
    ]);
    $report->pdf_path = 'weekly-reports/sample/source.pdf';
    $report->save();

    $this->actingAs($reporter, 'reporter')
        ->delete(route('reports.weekly-reports.destroy', $report))
        ->assertRedirect();

    $this->assertSoftDeleted($report);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.index'))
        ->assertDontSee('October social restore');

    $this->actingAs($reporter, 'reporter')
        ->post(route('reports.weekly-reports.restore', $report))
        ->assertRedirect();

    expect($report->refresh()->trashed())->toBeFalse()
        ->and($report->pdf_path)->toBe('weekly-reports/sample/source.pdf');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.index'))
        ->assertSee('October social restore');
});

test('a reporter can permanently delete a removed report', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'title' => 'November weekly gone',
    ]);

    $this->actingAs($reporter, 'reporter')
        ->delete(route('reports.weekly-reports.force', $report))
        ->assertNotFound();

    $report->delete();

    $this->actingAs($reporter, 'reporter')
        ->delete(route('reports.weekly-reports.force', $report))
        ->assertRedirect(route('reports.clients.show', $customer));

    $this->assertModelMissing($report);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.index'))
        ->assertDontSee('November weekly gone');
});
