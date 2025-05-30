<?php

namespace Modules\Essentials\Entities;

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;

class PayrollGroup extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'essentials_payroll_groups';

    /**
     * Get the transactions for the payroll group.
     */
    public function payrollGroupTransactions()
    {
        return $this->belongsToMany(Transaction::class, 'essentials_payroll_group_transactions', 'payroll_group_id', 'transaction_id');
    }

    /**
     * Get the location that owns the payroll group.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Get the business that owns the payroll group.
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
