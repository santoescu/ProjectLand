<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Project extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'projects';

    protected $fillable = [
        'name',
        'subprojects',
    ];

    public function getNameSubProjectAttribute()
    {

        if (empty($this->subprojects) || !is_array($this->subprojects)) {
            return '';
        }

        return implode('; ', $this->subprojects);
    }
}
