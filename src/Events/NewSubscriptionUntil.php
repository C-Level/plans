<?php

namespace Rennokki\Plans\Events;

use Illuminate\Queue\SerializesModels;

class NewSubscriptionUntil
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
     * @var \Carbon\Carbon
     */
    public $expiresOn;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model that subscribed.
     * @param \Rennokki\Plans\Models\PlanSubscriptionModel $subscription Subscription the model has subscribed to.
     * @param \Carbon\Carbon $expiresOn The date when the subscription expires.
     * @return void
     */
    public function __construct($model, $subscription, $expiresOn)
    {
        $this->model = $model;
        $this->subscription = $subscription;
        $this->expiresOn = $expiresOn;
    }
}
