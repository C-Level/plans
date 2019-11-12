<?php

namespace Rennokki\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanFeatureModel code($code)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanFeatureModel limited()
 * @method static \Illuminate\Database\Eloquent\Builder|\Rennokki\Plans\Models\PlanFeatureModel feature()
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rennokki\Plans\Models\PlanModel $plan
 */
class PlanFeatureModel extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'plan_features';

    /**
     * @inheritDoc
     */
    protected $guarded = [];

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'plan_id',
        'name',
        'code',
        'description',
        'type',
        'limit',
        'metadata'
    ];

    /**
     * @inheritDoc
     */
    protected $casts = [
        'metadata' => 'object',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(config('plans.models.plan'), 'plan_id');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLimited($query)
    {
        return $query->where('type', 'limit');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeature($query)
    {
        return $query->where('type', 'feature');
    }

    /**
     * @return bool
     */
    public function isUnlimited(): bool
    {
        return (bool)($this->type == 'limit' && $this->limit < 0);
    }
}
