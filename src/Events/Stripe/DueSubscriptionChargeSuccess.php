<?php

namespace Rennokki\Plans\Events\Stripe;

use Stripe\Charge as StripeCharge;
use Illuminate\Queue\SerializesModels;

class DueSubscriptionChargeSuccess
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
     * @var \Stripe\Charge
     */
    public $stripeCharge;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model on which the action was done.
     * @param \Rennokki\Plans\Models\PlanSubscriptionModel $subscription Due subscription that was paid.
     * @param \Stripe\Charge The result of the Stripe\Charge::create() call.
     * @return void
     */
    public function __construct($model, $subscription, StripeCharge $stripeCharge)
    {
        $this->model = $model;
        $this->subscription = $subscription;
        $this->stripeCharge = $stripeCharge;
    }
}
