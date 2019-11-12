<?php

namespace Rennokki\Plans\Events\Stripe;

use Stripe\Charge as StripeCharge;
use Illuminate\Queue\SerializesModels;

class ChargeSuccessful
{
    use SerializesModels;

    /**
     * @var \Rennokki\Plans\Models\PlanSubscriptionModel
     */
    public $model;

    /**
     * @var \Rennokki\Plans\Models\PlanSubscriptionModel
     */
    public $subscription;

    /**
     * @var \Stripe\Charge
     */
    public $stripeCharge;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model on which the action was done.
     * @param \Rennokki\Plans\Models\PlanSubscriptionModel $subscription Subscription that was paid.
     * @param \Stripe\Charge $stripeCharge
     */
    public function __construct($model, $subscription, StripeCharge $stripeCharge)
    {
        $this->model = $model;
        $this->subscription = $subscription;
        $this->stripeCharge = $stripeCharge;
    }
}
