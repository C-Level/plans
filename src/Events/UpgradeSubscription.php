<?php

namespace Rennokki\Plans\Events;

use Illuminate\Queue\SerializesModels;

class UpgradeSubscription
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
     * @var \Rennokki\Plans\Models\PlanModel|null
     */
    public $oldPlan;

    /**
     * @var \Rennokki\Plans\Models\PlanModel|null
     */
    public $newPlan;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model on which the action was done.
     * @param \Rennokki\Plans\Models\PlanSubscriptionModel $subscription Subscription that was upgraded.
     * @param bool $startFromNow Wether the current subscription is upgraded by extending now or is upgraded at the next cycle.
     * @param null|\Rennokki\Plans\Models\PlanModel $oldPlan The old plan.
     * @param null|\Rennokki\Plans\Models\PlanModel $newPlan The new plan.
     * @return void
     */
    public function __construct($model, $subscription, bool $startFromNow, $oldPlan, $newPlan)
    {
        $this->model = $model;
        $this->subscription = $subscription;
        $this->startFromNow = $startFromNow;
        $this->oldPlan = $oldPlan;
        $this->newPlan = $newPlan;
    }
}
