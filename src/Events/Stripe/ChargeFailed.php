<?php

namespace Rennokki\Plans\Events\Stripe;

use Illuminate\Queue\SerializesModels;

class ChargeFailed
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
     * @var
     */
    public $exception;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model The model on which the action was done.
     * @param \Rennokki\Plans\Models\PlanSubscriptionModel $subscription Subscription which wasn't paid.
     * @param \Exception The exception thrown by the Stripe\Charge::create() call.
     * @return void
     */
    public function __construct($model, $subscription, $exception)
    {
        $this->model = $model;
        $this->subscription = $subscription;
        $this->exception = $exception;
    }
}
