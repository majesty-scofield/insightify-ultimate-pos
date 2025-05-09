<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceScheme extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Returns list of invoice schemes in array format
     */
    public static function forDropdown($business_id)
    {
        return InvoiceScheme::where('business_id', $business_id)
                                ->pluck('name', 'id');
    }

    /**
     * Retrieves the default invoice scheme
     */
    public static function getDefault($business_id)
    {
        return InvoiceScheme::where('business_id', $business_id)
                                ->where('is_default', 1)
                                ->first();
    }
}
