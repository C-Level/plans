<?php

namespace Rennokki\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanSubscriptionUsageModel code($code)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rennokki\Plans\Models\PlanSubscriptionModel $subscription
 *
 */
class PlanSubscriptionUsageModel extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'plan_subscription_usages';

    /**
     * @inheritDoc
     */
    protected $guarded = [];

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'subscription_id',
        'code',
        'used'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(config('plans.models.subscription'), 'subscription_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
