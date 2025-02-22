<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function sub_categories()
    {
        return $this->hasMany(ExpenseCategory::class, 'parent_id');
    }

    /**
     * Scope a query to only include main categories.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeOnlyParent($query)
    {
        return $query->whereNull('parent_id');
    }
}
