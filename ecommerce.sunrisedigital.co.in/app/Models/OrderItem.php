<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'service_id',
    'plan_id',
    'service_name',
    'duration_label',
    'months',
    'price',
    'discount',
    'gst',
    'onboarding',
])]
class OrderItem extends Model
{
    use HasPublicUuid;

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'gst' => 'decimal:2',
            'onboarding' => 'array',
        ];
    }

    /**
     * @return list<array{prompt: string, help: ?string, required: bool, answer: ?string}>
     */
    public function onboardingQuestions(): array
    {
        $onboarding = $this->onboarding;

        if (! is_array($onboarding) || ! is_array($onboarding['questions'] ?? null)) {
            return [];
        }

        return array_values(array_filter($onboarding['questions'], fn (mixed $question): bool => is_array($question)));
    }

    public function needsOnboarding(): bool
    {
        return $this->onboardingQuestions() !== [] && ($this->onboarding['completed_at'] ?? null) === null;
    }

    public function onboardingComplete(): bool
    {
        return $this->onboardingQuestions() !== [] && ($this->onboarding['completed_at'] ?? null) !== null;
    }

    /**
     * Services this customer has paid for and not had refunded.
     *
     * @return list<int>
     */
    public static function purchasedServiceIds(?int $userId): array
    {
        if ($userId === null) {
            return [];
        }

        return self::query()
            ->whereNotNull('service_id')
            ->whereHas('order', function ($query) use ($userId): void {
                $query->where('user_id', $userId)->whereIn('status', ['placed', 'in_progress', 'delivered']);
            })
            ->pluck('service_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function money(): string
    {
        return '₹'.number_format((float) $this->price, 0, '.', ',');
    }
}
