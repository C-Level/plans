<?php

namespace Rennokki\Plans\Events;

use Illuminate\Queue\SerializesModels;

class FeatureConsumed
{
    use SerializesModels;

    /**
     * @var \Rennokki\Plans\Models\PlanSubscriptionModel
     */
    public $subscription;

    /**
     * @var \Rennokki\Plans\Models\PlanFeatureModel
     */
    public $feature;

    /**
     * @var float
     */
    public $used;

    /**
     * @var float
     */
    public $remaining;

    /**
     * @param \Rennokki\Plans\Models\PlanSubscriptionModel $subscription Subscription on which action was done.
     * @param \Rennokki\Plans\Models\PlanFeatureModel $feature The feature that was consumed.
     * @param float $used The amount used on this consumption.
     * @param float $remaining The amount remaining for this feature.
     * @return void
     */
    public function __construct($subscription, $feature, float $used, float $remaining)
    {
        $this->subscription = $subscription;
        $this->feature = $feature;
        $this->used = $used;
        $this->remaining = $remaining;
    }
}
