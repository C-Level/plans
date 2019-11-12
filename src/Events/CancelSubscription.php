<?php

namespace Rennokki\Plans\Events;

use Illuminate\Queue\SerializesModels;

class CancelSubscription
{
    use SerializesModels;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * @var \Rennokki\Plans\Models\PlanSubscriptionModel
     */
    public $subscription;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model on which the action was done.
     * @param \Rennokki\Plans\Models\PlanSubscriptionModel $subscription Subscription that was cancelled.
     * @return void
     */
    public function __construct($model, $subscription)
    {
        $this->model = $model;
        $this->subscription = $subscription;
    }
}
