<?php

namespace Modules\Essentials\Entities;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EssentialsTodoComment extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function added_by()
    {
        return $this->belongsTo(User::class, 'comment_by');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function task()
    {
        return $this->belongsTo(ToDo::class, 'task_id');
    }
}
