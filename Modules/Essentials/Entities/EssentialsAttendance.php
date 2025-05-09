<?php

namespace Modules\Essentials\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EssentialsAttendance extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'essentials_shift_id');
    }
}
