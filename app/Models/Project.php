<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Project extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'projects';

    protected $fillable = [
        'name',
        'project_id',
    ];

    public function getNameSubProjectAttribute()
    {
        if (! $this->project_id) {
            return "";
        }

        $subProject = self::find($this->project_id);

        return $subProject ? $subProject->name : "";
    }
}
