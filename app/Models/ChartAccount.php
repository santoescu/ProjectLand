<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ChartAccount extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'chartAccounts';

    protected $fillable = [
        'name',
        'parent_id',
    ];

    // Relación con el padre
    public function parent()
    {
        return $this->belongsTo(ChartAccount::class, 'parent_id');
    }

    // Relación con los hijos
    public function children()
    {
        return $this->hasMany(ChartAccount::class, 'parent_id');
    }

    public function getNameParentAttribute()
    {
        if (! $this->parent_id) {
            return "";
        }

        $parent = self::find($this->parent_id);

        return $parent ? $parent->name : "";
    }
}
