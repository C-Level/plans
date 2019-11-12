<?php

namespace Rennokki\Plans\Events;

use Illuminate\Queue\SerializesModels;

class ExtendSubscription
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
     * @var bool
     */
    public $startFromNow;

    /**
     * @var \Rennokki\Plans\Models\PlanSubscriptionModel|null
     */
    public $newSubscription;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model on which the action was done.
     * @param \Rennokki\Plans\Models\PlanSubscriptionModel $subscription Subscription that was extended.
     * @param bool $startFromNow Wether the current subscription is extended or is created at the next cycle.
     * @param null|\Rennokki\Plans\Models\PlanSubscriptionModel $newSubscription Null if $startFromNow is true; The new subscription created in extension.
     * @return void
     */
    public function __construct($model, $subscription, bool $startFromNow, $newSubscription)
    {
        $this->model = $model;
        $this->subscription = $subscription;
        $this->startFromNow = $startFromNow;
        $this->newSubscription = $newSubscription;
    }
}
