<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\HasPublicUuid;
use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'google_id',
    'billing_address',
    'billing_city',
    'billing_state',
    'billing_pin',
    'billing_gstin',
    'assigned_reporter_id',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasPublicUuid, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @return HasMany<WeeklyReport, $this>
     */
    public function weeklyReports(): HasMany
    {
        return $this->hasMany(WeeklyReport::class);
    }

    /**
     * @return BelongsTo<Reporter, $this>
     */
    public function assignedReporter(): BelongsTo
    {
        return $this->belongsTo(Reporter::class, 'assigned_reporter_id');
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function visibleToReporter(Reporter $reporter): bool
    {
        return $this->assigned_reporter_id === null || (int) $this->assigned_reporter_id === (int) $reporter->id;
    }

    public function scopeForReporter($query, Reporter $reporter)
    {
        return $query->where(function ($query) use ($reporter): void {
            $query->whereNull('assigned_reporter_id')->orWhere('assigned_reporter_id', $reporter->id);
        });
    }

    public function hasSavedBilling(): bool
    {
        if (blank($this->billing_state)) {
            return false;
        }

        if (blank($this->billing_gstin)) {
            return true;
        }

        return filled($this->billing_address) && filled($this->billing_city) && filled($this->billing_pin);
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));

        if (app()->environment('local') && config('mail.default') === 'log') {
            session()->flash('reset_url', url(route('password.reset', [
                'token' => $token,
                'email' => $this->getEmailForPasswordReset(),
            ], false)));
        }
    }
}
