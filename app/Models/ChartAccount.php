<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ChartAccount extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'chartAccounts';

    protected $fillable = [
        'name',
        'type',
    ];
}
