<?php

namespace Modules\Essentials\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EssentialsUserShift extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
