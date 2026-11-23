<?php

use App\Models\Admin;
use App\Models\Reporter;
use App\Models\User;
use App\Models\WeeklyReport;
use App\Support\MonthlyReportPdfReader;
use App\Support\WeeklyReportCharts;
use App\Support\WeeklyReportPdfReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function weeklyReportPdf(): UploadedFile
{
    return new UploadedFile(
        base_path('tests/Fixtures/nano-weekly-report.pdf'),
        'Nano Weekly Digital Marketing Performance Report.pdf',
        'application/pdf',
        null,
        true,
    );
}

function monthlyReportPdf(string $name): UploadedFile
{
    return new UploadedFile(
        base_path('tests/Fixtures/'.$name),
        $name,
        'application/pdf',
        null,
        true,
    );
}

function weeklyReportDocument(string $text): UploadedFile
{
    $lines = preg_split("/\r\n|\n|\r/", $text) ?: [];
    $commands = ['BT', '/F1 11 Tf', '40 1100 Td'];

    foreach ($lines as $index => $line) {
        if ($index > 0) {
            $commands[] = '0 -16 Td';
        }

        $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
        $commands[] = '('.$safe.') Tj';
    }

    $commands[] = 'ET';
    $stream = implode("\n", $commands);
    $objects = [
        "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n",
        "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n",
        "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 700 1200] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n",
        '4 0 obj << /Length '.strlen($stream)." >> stream\n".$stream."\nendstream\nendobj\n",
        "5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n",
    ];
    $pdf = "%PDF-1.4\n";
    $offsets = [0];

    foreach ($objects as $object) {
        $offsets[] = strlen($pdf);
        $pdf .= $object;
    }

    $xref = strlen($pdf);
    $pdf .= "xref\n0 6\n0000000000 65535 f \n";

    for ($i = 1; $i <= 5; $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }

    $pdf .= "trailer << /Size 6 /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
    $path = tempnam(sys_get_temp_dir(), 'report');
    file_put_contents($path, $pdf);

    return new UploadedFile($path, 'report.pdf', 'application/pdf', null, true);
}

test('the sample weekly pdf is read into the report figures', function () {
    $report = (new WeeklyReportPdfReader)->read(base_path('tests/Fixtures/nano-weekly-report.pdf'));

    expect($report['title'])->toBe('Nano Hospitals – Weekly SMM Performance Report')
        ->and($report['period_start'])->toBe('2026-11-08')
        ->and($report['period_end'])->toBe('2026-11-14')
        ->and($report['previous_start'])->toBe('2026-11-01')
        ->and($report['previous_end'])->toBe('2026-11-07')
        ->and($report['facebook']['current']['views'])->toBe('862')
        ->and($report['facebook']['current']['views_change'])->toBe('+5.3%')
        ->and($report['facebook']['current']['watch_time'])->toBe('52 minutes 51 seconds')
        ->and($report['facebook']['content']['total'])->toBe('13')
        ->and($report['facebook']['views_by_format']['reels'])->toBe('604')
        ->and($report['instagram']['current']['total_views'])->toBe('5.8K')
        ->and($report['instagram']['top'][0])->toBe(['title' => 'Pneumonia Awareness Reel', 'views' => '555'])
        ->and($report['youtube'][0]['name'])->toBe('BG Road')
        ->and($report['youtube'][0]['current']['views'])->toBe('651')
        ->and($report['youtube'][1]['name'])->toBe('Uttarahalli')
        ->and($report['youtube'][1]['current']['views'])->toBe('1,053')
        ->and($report['narrative']['working']['facebook'])->toContain('Views increased from 819 to 862')
        ->and($report['narrative']['working']['youtube'])->toContain('BG Road')
        ->and($report['narrative']['working']['youtube'])->toContain('Uttarahalli')
        ->and($report['narrative']['comparison']['instagram'])->toContain('Decreased by 18.6%');
});

test('charts draw the report figures to scale', function () {
    $report = new WeeklyReport([
        'facebook' => [
            'current' => ['views' => '862', 'watch_time' => '52 minutes 51 seconds'],
            'previous' => ['views' => '819', 'watch_time' => '1 hour 4 minutes'],
            'views_by_format' => ['reels' => '604', 'photos' => '189'],
        ],
        'instagram' => [
            'current' => ['total_views' => '5.8K'],
            'previous' => ['total_views' => '7.1K'],
        ],
        'youtube' => [[
            'name' => 'BG Road',
            'current' => ['views' => '1,053', 'watch_time' => '1.1 hours', 'stayed_to_watch' => '43.8%'],
            'previous' => ['views' => '534', 'watch_time' => '1.1 hours', 'stayed_to_watch' => '41.1%'],
        ]],
    ]);

    $charts = collect((new WeeklyReportCharts)->for($report))->keyBy('title');
    $viewsPrevious = collect($charts['Views']['pies'][0]['slices'])->keyBy('label');
    $viewsCurrent = collect($charts['Views']['pies'][1]['slices'])->keyBy('label');
    $watchPrevious = collect($charts['Watch time']['pies'][0]['slices'])->keyBy('label');
    $watchCurrent = collect($charts['Watch time']['pies'][1]['slices'])->keyBy('label');

    expect($charts['Views']['pies'][0]['label'])->toBe('Previous week')
        ->and($charts['Views']['pies'][1]['label'])->toBe('This week')
        ->and($viewsPrevious['Instagram']['amount'])->toBe(7100.0)
        ->and($viewsCurrent['Instagram']['amount'])->toBe(5800.0)
        ->and($viewsCurrent['YouTube · BG Road']['amount'])->toBe(1053.0)
        ->and($viewsCurrent['Instagram']['path'])->toStartWith('M ')
        ->and(round($watchCurrent['Facebook']['amount'], 2))->toBe(52.85)
        ->and($watchPrevious['Facebook']['amount'])->toBe(64.0)
        ->and($charts['Facebook views by format']['pies'][0]['slices'][0]['label'])->toBe('Reels');
});

test('the sample monthly social and seo pdfs are read into the report figures', function () {
    $reader = new MonthlyReportPdfReader;
    $social = $reader->read(base_path('tests/Fixtures/nano-september-2026-social.pdf'), 'social');
    $octoberSocial = $reader->read(base_path('tests/Fixtures/nano-october-2026-social.pdf'), 'social');
    $seo = $reader->read(base_path('tests/Fixtures/nano-september-2026-seo.pdf'), 'seo');
    $octoberSeo = $reader->read(base_path('tests/Fixtures/nano-october-2026-seo.pdf'), 'seo');

    expect($social['title'])->toBe('Nano Hospitals – September 2026 Social Media Report')
        ->and($social['period_start'])->toBe('2026-09-01')
        ->and($social['previous_start'])->toBe('2026-08-01')
        ->and($social['body']['instagram']['metrics'][0])->toBe(['label' => 'Views', 'value' => '64.1K'])
        ->and($social['body']['facebook']['metrics'][0]['value'])->toBe('8.3K')
        ->and($social['body']['youtube'][0]['name'])->toBe('BG Road')
        ->and($social['body']['youtube'][0]['metrics'][0]['value'])->toBe('6,228')
        ->and($social['body']['profiles'][0]['metrics'][0])->toBe(['label' => 'Interactions', 'value' => '6,008'])
        ->and($octoberSocial['body']['instagram']['metrics'][0]['value'])->toBe('70.9K')
        ->and($octoberSocial['body']['facebook']['metrics'][0]['value'])->toBe('7.4K')
        ->and($octoberSocial['body']['profiles'][1]['metrics'][0]['value'])->toBe('2,902')
        ->and($seo['title'])->toBe('Nano Hospitals – September 2026 SEO Performance Report')
        ->and($seo['period_start'])->toBe('2026-09-01')
        ->and($seo['previous_start'])->toBe('2026-08-01')
        ->and($seo['body']['comparison'][0])->toBe(['label' => 'Total Clicks', 'previous' => '2.07K', 'current' => '2.32K'])
        ->and($seo['body']['analytics'][4])->toMatchArray(['label' => 'WhatsApp Clicks', 'previous' => '59', 'current' => '61'])
        ->and($seo['body']['technical'][0])->toBe(['label' => 'Site Health', 'value' => '86%'])
        ->and($seo['body']['platforms'][0]['clicks'])->toBe('65')
        ->and($octoberSeo['period_start'])->toBe('2026-10-01')
        ->and($octoberSeo['previous_start'])->toBe('2026-09-01')
        ->and($octoberSeo['body']['platforms'][2]['name'])->toBe('Google Image Search')
        ->and($octoberSeo['body']['priorities'][0]['name'])->toBe('Cath Lab & Cardiology')
        ->and($octoberSeo['body']['comparison'][4])->toMatchArray(['label' => 'New Users', 'previous' => '4.5K', 'current' => '5.6k']);
});

test('a reporter can publish a social media report and an seo report for the same month', function () {
    Storage::fake('local');

    $customer = User::factory()->create();
    $reporter = Reporter::factory()->create();

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.clients.show', $customer))
        ->assertOk()
        ->assertSee('Social media report')
        ->assertSee('SEO report');

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.weekly-reports.create', ['customer' => $customer, 'kind' => 'social']))
        ->assertOk()
        ->assertSee('Upload the social media PDF')
        ->assertSee('New social media report');

    $this->actingAs($reporter, 'reporter')
        ->post(route('reports.weekly-reports.store', $customer), [
            'kind' => 'social',
            'intent' => 'publish',
            'pdf' => monthlyReportPdf('nano-september-2026-social.pdf'),
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($reporter, 'reporter')
        ->post(route('reports.weekly-reports.store', $customer), [
            'kind' => 'seo',
            'intent' => 'publish',
            'pdf' => monthlyReportPdf('nano-september-2026-seo.pdf'),
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($reporter, 'reporter')
        ->post(route('reports.weekly-reports.store', $customer), [
            'kind' => 'social',
            'intent' => 'publish',
            'pdf' => monthlyReportPdf('nano-september-2026-social.pdf'),
        ])
        ->assertSessionHasErrors([
            'pdf' => 'This client already has a social media report for that month.',
        ]);

    $social = WeeklyReport::query()->whereBelongsTo($customer, 'client')->where('kind', 'social')->firstOrFail();
    $seo = WeeklyReport::query()->whereBelongsTo($customer, 'client')->where('kind', 'seo')->firstOrFail();

    expect($social->isPublished())->toBeTrue()
        ->and($social->period_start->toDateString())->toBe('2026-09-01')
        ->and($social->body['instagram']['metrics'][0]['value'])->toBe('64.1K')
        ->and($seo->period_start->toDateString())->toBe('2026-09-01')
        ->and($seo->body['comparison'][0]['current'])->toBe('2.32K');

    auth('reporter')->logout();

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $social))
        ->assertOk()
        ->assertSee('Monthly social media report')
        ->assertSee('Each slice is that platform\'s share of views this month.')
        ->assertSee('64.1K')
        ->assertSee('6,008');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $seo))
        ->assertOk()
        ->assertSee('Monthly SEO report')
        ->assertSee('Each circle is one month, so you can compare new and returning users.')
        ->assertSee('2.32K')
        ->assertSee('August 2026');
});

test('a client can compare a social report with the previous published month', function () {
    $customer = User::factory()->create();
    $other = User::factory()->create();

    $september = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'social',
        'title' => 'September social',
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
        'previous_start' => '2026-08-01',
        'previous_end' => '2026-08-31',
        'body' => [
            'instagram' => ['metrics' => [['label' => 'Views', 'value' => '100']]],
            'facebook' => ['metrics' => [['label' => 'Views', 'value' => '80']]],
        ],
    ]);

    $october = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'social',
        'title' => 'October social',
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-31',
        'previous_start' => '2026-09-01',
        'previous_end' => '2026-09-30',
        'body' => [
            'instagram' => ['metrics' => [['label' => 'Views', 'value' => '150']]],
            'facebook' => ['metrics' => [['label' => 'Views', 'value' => '100']]],
        ],
    ]);

    $draft = WeeklyReport::factory()->create([
        'user_id' => $customer->id,
        'kind' => 'social',
        'title' => 'July social draft',
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'previous_start' => '2026-06-01',
        'previous_end' => '2026-06-30',
        'body' => [
            'instagram' => ['metrics' => [['label' => 'Views', 'value' => '10']]],
        ],
    ]);

    $foreign = WeeklyReport::factory()->published()->create([
        'user_id' => $other->id,
        'kind' => 'social',
        'title' => 'Other client social',
        'period_start' => '2026-08-01',
        'period_end' => '2026-08-31',
        'previous_start' => '2026-07-01',
        'previous_end' => '2026-07-31',
        'body' => [
            'instagram' => ['metrics' => [['label' => 'Views', 'value' => '999']]],
        ],
    ]);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.index'))
        ->assertSee('Social media')
        ->assertSee('October 2026')
        ->assertSee('September 2026')
        ->assertDontSee('July 2026');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $october))
        ->assertSee('Each circle is one month, so you can compare the two months.')
        ->assertSee('September 2026')
        ->assertSee('October 2026')
        ->assertSee('+50%')
        ->assertSee(route('dashboard.reports.show', $september), false)
        ->assertDontSee(route('dashboard.reports.show', $draft), false)
        ->assertDontSee(route('dashboard.reports.show', $foreign), false);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $september))
        ->assertSee('September 2026')
        ->assertSee('October 2026')
        ->assertSee('+50%')
        ->assertDontSee('999');
});

test('a comparison includes a platform that only the later report has', function () {
    $customer = User::factory()->create();
    $september = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'seo',
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
        'body' => [
            'platforms' => [
                ['name' => 'Instagram', 'clicks' => '65'],
            ],
        ],
    ]);
    $october = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'seo',
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-31',
        'body' => [
            'platforms' => [
                ['name' => 'Instagram', 'clicks' => '58'],
                ['name' => 'Google Image Search', 'clicks' => '47'],
            ],
        ],
    ]);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', ['report' => $september, 'compare' => $october->uuid]))
        ->assertSee('Google Image Search')
        ->assertSee('47')
        ->assertSee('September 2026')
        ->assertSee('October 2026');
});

test('a client can compare a report with any other published report of the same kind', function () {
    $customer = User::factory()->create();
    $other = User::factory()->create();

    $july = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'social',
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'previous_start' => '2026-06-01',
        'previous_end' => '2026-06-30',
        'body' => ['instagram' => ['metrics' => [['label' => 'Views', 'value' => '100']]]],
    ]);
    WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'social',
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
        'previous_start' => '2026-08-01',
        'previous_end' => '2026-08-31',
        'body' => ['instagram' => ['metrics' => [['label' => 'Views', 'value' => '4,000']]]],
    ]);
    $october = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'social',
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-31',
        'previous_start' => '2026-09-01',
        'previous_end' => '2026-09-30',
        'body' => ['instagram' => ['metrics' => [['label' => 'Views', 'value' => '150']]]],
    ]);
    $foreign = WeeklyReport::factory()->published()->create([
        'user_id' => $other->id,
        'kind' => 'social',
        'period_start' => '2026-08-01',
        'period_end' => '2026-08-31',
        'previous_start' => '2026-07-01',
        'previous_end' => '2026-07-31',
        'body' => ['instagram' => ['metrics' => [['label' => 'Views', 'value' => '4242']]]],
    ]);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $october))
        ->assertSee('Compare with')
        ->assertSee('July 2026')
        ->assertSee('4,000');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', ['report' => $october, 'compare' => $july->uuid]))
        ->assertSee('July 2026')
        ->assertSee('October 2026')
        ->assertSee('+50%')
        ->assertDontSee('4242');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', ['report' => $july, 'compare' => $october->uuid]))
        ->assertSee('July 2026')
        ->assertSee('October 2026')
        ->assertSee('+50%');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', ['report' => $october, 'compare' => $foreign->uuid]))
        ->assertSee('4,000')
        ->assertDontSee('4242');

    $earlierWeek = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'weekly',
        'period_start' => '2026-11-01',
        'period_end' => '2026-11-07',
        'previous_start' => '2026-10-25',
        'previous_end' => '2026-10-31',
        'facebook' => ['current' => ['views' => '400'], 'previous' => ['views' => '10']],
        'instagram' => ['current' => ['total_views' => '100'], 'previous' => ['total_views' => '10']],
        'youtube' => [],
    ]);
    $laterWeek = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'weekly',
        'period_start' => '2026-11-08',
        'period_end' => '2026-11-14',
        'previous_start' => '2026-11-01',
        'previous_end' => '2026-11-07',
        'facebook' => ['current' => ['views' => '500'], 'previous' => ['views' => '10']],
        'instagram' => ['current' => ['total_views' => '100'], 'previous' => ['total_views' => '10']],
        'youtube' => [],
    ]);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', ['report' => $laterWeek, 'compare' => $earlierWeek->uuid]))
        ->assertSee('1–7 November 2026')
        ->assertSee('8–14 November 2026')
        ->assertSee('+25%');

    $earlierSeo = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'seo',
        'period_start' => '2026-07-01',
        'period_end' => '2026-07-31',
        'previous_start' => '2026-06-01',
        'previous_end' => '2026-06-30',
        'body' => ['comparison' => [['label' => 'New Users', 'previous' => '9', 'current' => '100']]],
    ]);
    $laterSeo = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'kind' => 'seo',
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-31',
        'previous_start' => '2026-09-01',
        'previous_end' => '2026-09-30',
        'body' => ['comparison' => [['label' => 'New Users', 'previous' => '1', 'current' => '200']]],
    ]);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', ['report' => $laterSeo, 'compare' => $earlierSeo->uuid]))
        ->assertSee('July 2026')
        ->assertSee('October 2026')
        ->assertSee('+100%')
        ->assertDontSee('+19900%');
});

test('a reporter can open the weekly report form', function () {
    $customer = User::factory()->create(['name' => 'Nano Hospitals']);

    $this->actingAs(Reporter::factory()->create(), 'reporter')
        ->get(route('reports.weekly-reports.create', $customer))
        ->assertOk()
        ->assertSee('Nano Hospitals')
        ->assertSee('Upload the weekly PDF')
        ->assertSee('Publish');
});

test('a reporter can publish a weekly report the client can open', function () {
    Storage::fake('local');

    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create(['name' => 'Nano Hospitals']);
    $other = User::factory()->create();

    $this->actingAs($reporter, 'reporter')
        ->post(route('reports.weekly-reports.store', $customer), [
            'pdf' => weeklyReportPdf(),
            'intent' => 'publish',
            'user_id' => $other->id,
            'status' => 'draft',
        ])
        ->assertRedirect(route('reports.clients.show', $customer))
        ->assertSessionHasNoErrors();

    $report = WeeklyReport::query()->whereBelongsTo($customer, 'client')->firstOrFail();

    expect($report->user_id)->toBe($customer->id)
        ->and($report->reporter_id)->toBe($reporter->id)
        ->and($report->isPublished())->toBeTrue()
        ->and($report->facebook['current']['views'])->toBe('862')
        ->and($report->facebook['current']['views_change'])->toBe('+5.3%')
        ->and($report->youtube[0]['name'])->toBe('BG Road')
        ->and($report->pdf_path)->not->toBeNull();

    Storage::disk('local')->assertExists($report->pdf_path);

    auth('reporter')->logout();

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $report))
        ->assertOk()
        ->assertSee('Nano Hospitals')
        ->assertSee('8–14 November 2026')
        ->assertSee('862')
        ->assertSee('(+5.3%)')
        ->assertSee('BG Road')
        ->assertSee('651')
        ->assertSee('Open the PDF')
        ->assertSee('Uttarahalli')
        ->assertSee('At a glance')
        ->assertSee('Each circle is one week, so you can compare the two weeks.')
        ->assertSee('Facebook views by format')
        ->assertSee('Watch time');

    $this->actingAs($customer, 'web')
        ->get(route('weekly-reports.pdf', $report))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    $this->actingAs($other, 'web')
        ->get(route('dashboard.reports.show', $report))
        ->assertNotFound();

    $this->actingAs($other, 'web')
        ->get(route('weekly-reports.pdf', $report))
        ->assertNotFound();
});

test('a draft stays off the client dashboard until it is published', function () {
    Storage::fake('local');

    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();

    $this->actingAs($reporter, 'reporter')
        ->post(route('reports.weekly-reports.store', $customer), [
            'pdf' => weeklyReportPdf(),
            'intent' => 'draft',
        ])
        ->assertRedirect(route('reports.clients.show', $customer));

    $report = WeeklyReport::query()->whereBelongsTo($customer, 'client')->firstOrFail();

    expect($report->isPublished())->toBeFalse();

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.weekly-reports.show', $report))
        ->assertOk()
        ->assertSee('The client cannot see this report until it is published.')
        ->assertSee('862');

    auth('reporter')->logout();

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $report))
        ->assertNotFound();

    $this->actingAs($customer, 'web')
        ->get(route('weekly-reports.pdf', $report))
        ->assertNotFound();

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.index'))
        ->assertOk()
        ->assertDontSee('Nano Hospitals – Weekly SMM Performance Report');
});

test('saving a published report as a draft hides it from the client', function () {
    Storage::fake('local');

    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'pdf_path' => 'weekly-reports/1/source.pdf',
        'pdf_original_name' => 'report.pdf',
    ]);
    Storage::disk('local')->put($report->pdf_path, 'pdf');

    $this->actingAs($reporter, 'reporter')
        ->put(route('reports.weekly-reports.update', $report), [
            'intent' => 'draft',
        ])
        ->assertRedirect(route('reports.clients.show', $customer));

    expect($report->refresh()->isPublished())->toBeFalse()
        ->and($report->pdf_path)->toBe('weekly-reports/1/source.pdf');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $report))
        ->assertNotFound();
});

test('a report cannot be published without a pdf', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();

    $this->actingAs($reporter, 'reporter')
        ->post(route('reports.weekly-reports.store', $customer), [
            'intent' => 'publish',
        ])
        ->assertSessionHasErrors([
            'pdf' => 'Upload the report PDF.',
        ]);

    expect(WeeklyReport::query()->count())->toBe(0);
});

test('a client cannot have two reports for the same week', function () {
    Storage::fake('local');

    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    WeeklyReport::factory()->create([
        'user_id' => $customer->id,
        'period_start' => '2026-11-08',
        'period_end' => '2026-11-14',
        'previous_start' => '2026-11-01',
        'previous_end' => '2026-11-07',
    ]);

    $this->actingAs($reporter, 'reporter')
        ->post(route('reports.weekly-reports.store', $customer), [
            'pdf' => weeklyReportPdf(),
            'intent' => 'draft',
        ])
        ->assertSessionHasErrors([
            'pdf' => 'This client already has a weekly report for that week.',
        ]);
});

test('the previous week must end before the current week starts', function () {
    $this->actingAs(Reporter::factory()->create(), 'reporter')
        ->post(route('reports.weekly-reports.store', User::factory()->create()), [
            'pdf' => weeklyReportDocument(<<<'TEXT'
Example – Weekly SMM Performance Report
Current Week: 1-7 November 2026
Previous Week: 8-14 November 2026
TEXT),
            'intent' => 'draft',
        ])
        ->assertSessionHasErrors([
            'pdf' => 'The previous week must end before this week starts.',
        ]);
});

test('a report file has to be a pdf', function () {
    $this->actingAs(Reporter::factory()->create(), 'reporter')
        ->post(route('reports.weekly-reports.store', User::factory()->create()), [
            'pdf' => UploadedFile::fake()->create('notes.txt', 20, 'text/plain'),
            'intent' => 'draft',
        ])
        ->assertSessionHasErrors([
            'pdf' => 'The report must be a PDF.',
        ]);
});

test('a pdf without the weekly report headings is rejected', function () {
    $this->actingAs(Reporter::factory()->create(), 'reporter')
        ->post(route('reports.weekly-reports.store', User::factory()->create()), [
            'pdf' => weeklyReportDocument('Just a note about last week.'),
            'intent' => 'draft',
        ])
        ->assertSessionHasErrors([
            'pdf' => 'This PDF does not look like a weekly SMM report. It needs a title, the current week, and the previous week.',
        ]);

    expect(WeeklyReport::query()->count())->toBe(0);
});

test('written summary text from the pdf is escaped for the client', function () {
    Storage::fake('local');

    $customer = User::factory()->create();

    $this->actingAs(Reporter::factory()->create(), 'reporter')
        ->post(route('reports.weekly-reports.store', $customer), [
            'pdf' => weeklyReportDocument(<<<'TEXT'
Example – Weekly SMM Performance Report
Current Week: 8-14 November 2026
Previous Week: 1-7 November 2026
1. What Is Working?
Facebook
- Views increased <script>alert('xss')</script>
TEXT),
            'intent' => 'publish',
        ])
        ->assertSessionHasNoErrors();

    $report = WeeklyReport::query()->whereBelongsTo($customer, 'client')->firstOrFail();

    auth('reporter')->logout();

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $report))
        ->assertOk()
        ->assertSee('<script>')
        ->assertDontSee("<script>alert('xss')</script>", false);
});

test('replacing a report keeps the new pdf on that report only', function () {
    Storage::fake('local');

    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'period_start' => '2026-11-15',
        'period_end' => '2026-11-21',
        'previous_start' => '2026-11-08',
        'previous_end' => '2026-11-14',
        'pdf_path' => 'weekly-reports/old/source.pdf',
    ]);
    Storage::disk('local')->put($report->pdf_path, 'old');

    $this->actingAs($reporter, 'reporter')
        ->put(route('reports.weekly-reports.update', $report), [
            'pdf' => weeklyReportPdf(),
            'intent' => 'publish',
        ])
        ->assertSessionHasNoErrors();

    $report->refresh();

    expect($report->period_start->toDateString())->toBe('2026-11-08')
        ->and($report->facebook['current']['views'])->toBe('862')
        ->and($report->isPublished())->toBeTrue();

    Storage::disk('local')->assertExists($report->pdf_path);
    Storage::disk('local')->assertMissing('weekly-reports/old/source.pdf');
});

test('a reporter can edit or delete a report from its page', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create(['name' => 'North Clinic']);
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
    ]);

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.weekly-reports.show', $report))
        ->assertSee('Edit')
        ->assertSee('Delete')
        ->assertSee(route('reports.weekly-reports.edit', $report), false)
        ->assertSee(route('reports.weekly-reports.destroy', $report), false)
        ->assertSee('Back to North Clinic');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $report))
        ->assertSee('Your reports')
        ->assertDontSee('Delete this report?')
        ->assertDontSee(route('reports.weekly-reports.edit', $report), false);
});

test('a reporter can correct a figure without replacing the pdf', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'facebook' => [
            'current' => ['views' => '862'],
            'previous' => ['views' => '819'],
        ],
    ]);
    $report->pdf_path = 'weekly-reports/sample/source.pdf';
    $report->save();

    $social = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'kind' => 'social',
        'period_start' => '2026-10-01',
        'period_end' => '2026-10-31',
        'previous_start' => '2026-09-01',
        'previous_end' => '2026-09-30',
        'body' => [
            'instagram' => ['metrics' => [['label' => 'Views', 'value' => '64.1K']]],
        ],
    ]);
    $social->pdf_path = 'weekly-reports/sample/social.pdf';
    $social->save();

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.weekly-reports.edit', $report))
        ->assertSee('Correct a number')
        ->assertSee('name="figures[0][path]"', false)
        ->assertSee('862');

    $this->actingAs($reporter, 'reporter')
        ->put(route('reports.weekly-reports.update', $report), [
            'intent' => 'publish',
            'kind' => 'weekly',
            'figures' => [
                ['path' => 'facebook.current.views', 'value' => '900'],
            ],
        ])
        ->assertSessionHasNoErrors();

    expect($report->refresh()->facebook['current']['views'])->toBe('900')
        ->and($report->facebook['previous']['views'])->toBe('819')
        ->and($report->pdf_path)->toBe('weekly-reports/sample/source.pdf');

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.show', $report))
        ->assertSee('900');

    $this->actingAs($reporter, 'reporter')
        ->put(route('reports.weekly-reports.update', $report), [
            'intent' => 'publish',
            'figures' => [
                ['path' => 'title', 'value' => 'Replaced'],
            ],
        ])
        ->assertSessionHasErrors('figures');

    $this->actingAs($reporter, 'reporter')
        ->put(route('reports.weekly-reports.update', $social), [
            'intent' => 'publish',
            'figures' => [
                ['path' => 'body.instagram.metrics.0.value', 'value' => '70.9K'],
            ],
        ])
        ->assertSessionHasNoErrors();

    expect($social->refresh()->body['instagram']['metrics'][0]['value'])->toBe('70.9K');
});

test('a reporter can publish a drafted social report when the commentary is longer than a figure', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $commentary = 'September content focused on a balanced mix of health awareness, hospital information, celebrations and educational communication.';
    $report = WeeklyReport::factory()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'kind' => 'social',
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
        'previous_start' => '2026-08-01',
        'previous_end' => '2026-08-31',
        'body' => [
            'instagram' => ['metrics' => [['label' => 'Views', 'value' => '64.1K']]],
            'content' => [
                $commentary,
                'The content mix supported both informative value and audience engagement while creating opportunities for higher visibility.',
            ],
        ],
    ]);
    $report->pdf_path = 'weekly-reports/sample/social.pdf';
    $report->save();

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.weekly-reports.edit', $report))
        ->assertSee('value="body.instagram.metrics.0.value"', false)
        ->assertSee('64.1K')
        ->assertDontSee('body.content.', false)
        ->assertDontSee($commentary);

    $this->actingAs($reporter, 'reporter')
        ->put(route('reports.weekly-reports.update', $report), [
            'intent' => 'publish',
            'figures' => [
                ['path' => 'body.instagram.metrics.0.value', 'value' => '70.9K'],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('reports.clients.show', $customer));

    $report->refresh();

    expect($report->isPublished())->toBeTrue()
        ->and($report->body['instagram']['metrics'][0]['value'])->toBe('70.9K')
        ->and($report->body['content'][0])->toBe($commentary)
        ->and($report->body['content'][1])->toBe('The content mix supported both informative value and audience engagement while creating opportunities for higher visibility.');
});

test('a reporter can correct a weekly content count', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
        'facebook' => [
            'content' => ['reels' => '5', 'stories' => '2'],
        ],
    ]);
    $report->pdf_path = 'weekly-reports/sample/source.pdf';
    $report->save();

    $this->actingAs($reporter, 'reporter')
        ->get(route('reports.weekly-reports.edit', $report))
        ->assertSee('value="facebook.content.reels"', false)
        ->assertSee('value="facebook.content.stories"', false);

    $this->actingAs($reporter, 'reporter')
        ->put(route('reports.weekly-reports.update', $report), [
            'intent' => 'publish',
            'figures' => [
                ['path' => 'facebook.content.reels', 'value' => '8'],
                ['path' => 'facebook.content.stories', 'value' => '2'],
            ],
        ])
        ->assertSessionHasNoErrors();

    expect($report->refresh()->facebook['content']['reels'])->toBe('8')
        ->and($report->facebook['content']['stories'])->toBe('2');
});

test('deleting a report removes it from the client', function () {
    $reporter = Reporter::factory()->create();
    $customer = User::factory()->create();
    $report = WeeklyReport::factory()->published()->create([
        'user_id' => $customer->id,
        'reporter_id' => $reporter->id,
    ]);

    $this->actingAs($reporter, 'reporter')
        ->delete(route('reports.weekly-reports.destroy', $report))
        ->assertRedirect(route('reports.clients.show', $customer));

    $this->assertSoftDeleted($report);

    $this->actingAs($customer, 'web')
        ->get(route('dashboard.reports.index'))
        ->assertOk()
        ->assertDontSee($report->title);
});

test('an admin does not file reports from the reports dashboard', function () {
    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get(route('reports.home'))
        ->assertRedirect(route('reports.login'));
});

test('a guest cannot open a report pdf', function () {
    Storage::fake('local');

    $report = WeeklyReport::factory()->published()->create([
        'pdf_path' => 'weekly-reports/guest/source.pdf',
    ]);
    Storage::disk('local')->put($report->pdf_path, 'pdf');

    $this->get(route('weekly-reports.pdf', $report))
        ->assertNotFound();
});
