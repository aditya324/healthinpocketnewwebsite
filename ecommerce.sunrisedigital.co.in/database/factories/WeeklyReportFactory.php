<?php

namespace Database\Factories;

use App\Models\Reporter;
use App\Models\User;
use App\Models\WeeklyReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<WeeklyReport>
 */
class WeeklyReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->startOfWeek(Carbon::SUNDAY);

        return [
            'user_id' => User::factory(),
            'reporter_id' => Reporter::factory(),
            'title' => 'Weekly SMM Performance Report',
            'period_start' => $start->toDateString(),
            'period_end' => $start->copy()->addDays(6)->toDateString(),
            'previous_start' => $start->copy()->subWeek()->toDateString(),
            'previous_end' => $start->copy()->subDay()->toDateString(),
            'status' => 'draft',
            'facebook' => [
                'current' => ['views' => '862', 'views_change' => '+5.3%'],
                'previous' => ['views' => '819'],
            ],
            'instagram' => [
                'current' => ['total_views' => '5.8K'],
                'previous' => ['total_views' => '7.1K'],
                'top' => [
                    ['title' => 'Pneumonia Awareness Reel', 'views' => '555'],
                ],
            ],
            'youtube' => [
                [
                    'name' => 'BG Road',
                    'current' => ['views' => '651'],
                    'previous' => ['views' => '534'],
                ],
            ],
            'narrative' => [
                'working' => ['facebook' => 'Views increased from 819 to 862.'],
                'not_working' => ['instagram' => 'Total views declined from 7.1K to 5.8K.'],
                'comparison' => ['facebook' => 'Views increased by 5.3%.'],
            ],
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
