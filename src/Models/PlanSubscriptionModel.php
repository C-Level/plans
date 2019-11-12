<?php

namespace Rennokki\Plans\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rennokki\Plans\Exceptions\UnsupportedPaymentMethodException;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanSubscriptionModel paid()
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanSubscriptionModel unpaid()
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanSubscriptionModel expired()
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanSubscriptionModel recurring()
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanSubscriptionModel cancelled()
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanSubscriptionModel notCancelled()
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanSubscriptionModel stripe()
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rennokki\Plans\Models\PlanModel $plan
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rennokki\Plans\Models\PlanSubscriptionUsageModel[] $usages
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rennokki\Plans\Models\PlanFeatureModel[] $features
 */
class PlanSubscriptionModel extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'plan_subscriptions';

    /**
     * @inheritDoc
     */
    protected $guarded = [];

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'plan_id',
        'model_id',
        'model_type',
        'payment_method',
        'is_paid',
        'charging_price',
        'charging_currency',
        'is_recurring',
        'recurring_each_days',
        'starts_on',
        'expires_on',
        'cancelled_on'
    ];

    /**
     * @inheritDoc
     */
    protected $dates = [
        'starts_on',
        'expires_on',
        'cancelled_on',
    ];

    /**
     * @inheritDoc
     */
    protected $casts = [
        'is_paid' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(config('plans.models.plan'), 'plan_id');
    }

    /**
     * @return \Rennokki\Plans\Models\PlanFeatureModel
     */
    public function features()
    {
        return $this->plan()->first()->features();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usages(): HasMany
    {
        return $this->hasMany(config('plans.models.usage'), 'subscription_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_on', '<', Carbon::now()->toDateTimeString());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled($query)
    {
        return $query->whereNotNull('cancelled_on');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotCancelled($query)
    {
        return $query->whereNull('cancelled_on');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStripe($query)
    {
        return $query->where('payment_method', 'stripe');
    }

    /**
     * Checks if the current subscription has started.
     *
     * @return bool
     */
    public function hasStarted()
    {
        return (bool)Carbon::now()->greaterThanOrEqualTo(Carbon::parse($this->starts_on));
    }

    /**
     * Checks if the current subscription has expired.
     *
     * @return bool
     */
    public function hasExpired()
    {
        return (bool)Carbon::now()->greaterThan(Carbon::parse($this->expires_on));
    }

    /**
     * Checks if the current subscription is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool)($this->hasStarted() && !$this->hasExpired());
    }

    /**
     * Get the remaining days in this subscription.
     *
     * @return int
     */
    public function remainingDays()
    {
        if ($this->hasExpired()) {
            return (int)0;
        }

        return (int)Carbon::now()->diffInDays(Carbon::parse($this->expires_on));
    }

    /**
     * Checks if the current subscription is cancelled (expiration date is in the past & the subscription is cancelled).
     *
     * @return bool
     */
    public function isCancelled()
    {
        return (bool)$this->cancelled_on != null;
    }

    /**
     * Checks if the current subscription is pending cancellation.
     *
     * @return bool
     */
    public function isPendingCancellation()
    {
        return (bool)($this->isCancelled() && $this->isActive());
    }

    /**
     * Cancel this subscription.
     *
     * @return self $this
     */
    public function cancel()
    {
        $this->update([
            'cancelled_on' => Carbon::now(),
        ]);

        return $this;
    }

    /**
     * Consume a feature, if it is 'limit' type.
     *
     * @param string $featureCode The feature code. This feature has to be 'limit' type.
     * @param float $amount The amount consumed.
     * @return bool Wether the feature was consumed successfully or not.
     */
    public function consumeFeature(string $featureCode, float $amount)
    {
        $usageModel = config('plans.models.usage');

        $feature = $this->features()->code($featureCode)->first();

        if (!$feature || $feature->type != 'limit') {
            return false;
        }

        $usage = $this->usages()->code($featureCode)->first();

        if (!$usage) {
            $usage = $this->usages()->save(new $usageModel([
                'code' => $featureCode,
                'used' => 0,
            ]));
        }

        if (!$feature->isUnlimited() && $usage->used + $amount > $feature->limit) {
            return false;
        }

        $remaining = (float)($feature->isUnlimited()) ? -1 : $feature->limit - ($usage->used + $amount);

        event(new \Rennokki\Plans\Events\FeatureConsumed($this, $feature, $amount, $remaining));

        return $usage->update([
            'used' => (float)($usage->used + $amount),
        ]);
    }

    /**
     * Reverse of the consume a feature method, if it is 'limit' type.
     *
     * @param string $featureCode The feature code. This feature has to be 'limit' type.
     * @param float $amount The amount consumed.
     * @return bool Wether the feature was consumed successfully or not.
     */
    public function unconsumeFeature(string $featureCode, float $amount)
    {
        $usageModel = config('plans.models.usage');

        $feature = $this->features()->code($featureCode)->first();

        if (!$feature || $feature->type != 'limit') {
            return false;
        }

        $usage = $this->usages()->code($featureCode)->first();

        if (!$usage) {
            $usage = $this->usages()->save(new $usageModel([
                'code' => $featureCode,
                'used' => 0,
            ]));
        }

        $used = (float)($feature->isUnlimited()) ? ($usage->used - $amount < 0) ? 0 : ($usage->used - $amount) : ($usage->used - $amount);

        $remaining = (float)($feature->isUnlimited()) ? -1 : ($used > 0) ? ($feature->limit - $used) : $feature->limit;

        event(new \Rennokki\Plans\Events\FeatureUnconsumed($this, $feature, $amount, $remaining));

        return $usage->update([
            'used' => $used,
        ]);
    }

    /**
     * Get the amount used for a limit.
     *
     * @param string $featureCode The feature code. This feature has to be 'limit' type.
     * @return void|null|float Null if doesn't exist, integer with the usage.
     */
    public function getUsageOf(string $featureCode)
    {
        $usage = $this->usages()->code($featureCode)->first();

        $feature = $this->features()->code($featureCode)->first();

        if (!$feature || $feature->type != 'limit') {
            return;
        }

        if (!$usage) {
            return 0;
        }

        return (float)$usage->used;
    }

    /**
     * Get the amount remaining for a feature.
     *
     * @param string $featureCode The feature code. This feature has to be 'limit' type.
     * @return float The amount remaining.
     */
    public function getRemainingOf(string $featureCode)
    {
        $usage = $this->usages()->code($featureCode)->first();

        $feature = $this->features()->code($featureCode)->first();

        if (!$feature || $feature->type != 'limit') {
            return 0;
        }

        if (!$usage) {
            return (float)($feature->isUnlimited()) ? -1 : $feature->limit;
        }

        return (float)($feature->isUnlimited()) ? -1 : ($feature->limit - $usage->used);
    }

    /**
     * Supported payment methods in the plans.payment_methods array
     * If we need add a new payment method, just append the array and use all system.
     *
     * @param mixed $value Payment Method
     * @throws \Rennokki\Plans\Exceptions\UnsupportedPaymentMethodException
     */
    public function setPaymentMethodAttribute($value)
    {
        if (!is_null($value)) {
            $supportedPaymentMethods = config('plans.payment_methods', ['stripe']);
            if (!in_array($value, $supportedPaymentMethods)) {
                throw new UnsupportedPaymentMethodException(sprintf('The payment method (%s) does not supported. Supported payment methods: %s', $value, implode(', ', $supportedPaymentMethods)));
            }
        } else {
            $this->attributes['payment_method'] = $value;
        }
    }
}
