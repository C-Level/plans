<?php

namespace Rennokki\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rennokki\Plans\Models\PlanFeatureModel[] $features
 */
class PlanModel extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'plans';

    /**
     * @inheritDoc
     */
    protected $guarded = [];

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'duration',
        'metadata'
    ];

    /**
     * @inheritDoc
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function features(): HasMany
    {
        return $this->hasMany(config('plans.models.feature'), 'plan_id');
    }

    /**
     * @param null $property
     * @return mixed
     */
    public function getMetaData($property = null)
    {
        if (is_null($property)) {
            return $this->metadata;
        }

        return $this->metadata[$property];
    }
}
