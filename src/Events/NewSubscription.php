<?php

namespace Rennokki\Plans\Events;

use Illuminate\Queue\SerializesModels;

class NewSubscription
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
     * @param \Illuminate\Database\Eloquent\Model $model The model that subscribed.
     * @param \Rennokki\Plans\Models\PlanSubscriptionModel $subscription Subscription the model has subscribed to.
     * @return void
     */
    public function __construct($model, $subscription)
    {
        $this->model = $model;
        $this->subscription = $subscription;
    }
}
