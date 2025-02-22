<?php

namespace Modules\Essentials\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EssentialsMessage extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get sender.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
