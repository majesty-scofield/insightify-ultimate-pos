<?php

namespace App\Restaurant;

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //Allowed booking statuses ('waiting', 'booked', 'completed', 'cancelled')

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function table()
    {
        return $this->belongsTo(ResTable::class, 'table_id');
    }

    public function correspondent()
    {
        return $this->belongsTo(User::class, 'correspondent_id');
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public static function createBooking($input)
    {
        $data = [
            'contact_id' => $input['contact_id'],
            'waiter_id' => isset($input['res_waiter_id']) ? $input['res_waiter_id'] : null,
            'table_id' => isset($input['res_table_id']) ? $input['res_table_id'] : null,
            'business_id' => $input['business_id'],
            'location_id' => $input['location_id'],
            'correspondent_id' => isset($input['correspondent']) ? $input['correspondent'] : null,
            'booking_start' => $input['booking_start'],
            'booking_end' => $input['booking_end'],
            'created_by' => $input['created_by'],
            'booking_status' => isset($input['booking_status']) ? $input['booking_status'] : 'booked',
            'booking_note' => $input['booking_note'],
        ];
        return Booking::create($data);
    }
}
