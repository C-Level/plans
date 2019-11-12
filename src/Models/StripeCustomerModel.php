<?php

namespace Rennokki\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StripeCustomerModel extends Model
{
    /**
     * @inheritDoc
     */
    protected $table = 'stripe_customers';

    /**
     * @inheritDoc
     */
    protected $guarded = [];

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'customer_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
