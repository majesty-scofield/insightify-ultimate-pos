<?php

namespace Modules\Essentials\Entities;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ToDo extends Model
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
    protected $table = 'essentials_to_dos';

    public function users()
    {
        return $this->belongsToMany(User::class, 'essentials_todos_users', 'todo_id', 'user_id');
    }

    public function assigned_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(EssentialsTodoComment::class, 'task_id')->orderBy('id', 'desc');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public static function getTaskStatus()
    {
        $statuses = [
            'new' => __('essentials::lang.new'),
            'in_progress' => __('essentials::lang.in_progress'),
            'on_hold' => __('essentials::lang.on_hold'),
            'completed' => __('restaurant.completed'),
        ];

        return $statuses;
    }

    public static function getTaskPriorities()
    {
        $priorities = [
            'low' => __('essentials::lang.low'),
            'medium' => __('essentials::lang.medium'),
            'high' => __('essentials::lang.high'),
            'urgent' => __('essentials::lang.urgent'),
        ];

        return $priorities;
    }

    /**
     * Attributes to be logged for activity
     */
    public function getLogPropertiesAttribute()
    {
        $properties = ['status'];

        return $properties;
    }
}
