<?php

namespace App\Listeners;

use App\Models\AccountTransaction;
use App\Utils\ModuleUtil;

class UpdateAccountTransaction
{
    protected $moduleUtil;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (! $this->moduleUtil->isModuleEnabled('account')) {
            return true;
        }

        AccountTransaction::updateAccountTransaction($event->transactionPayment, $event->transactionType);
    }
}
