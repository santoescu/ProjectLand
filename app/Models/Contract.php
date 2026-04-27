<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Contract extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contracts';

    protected $fillable = [
        'name',
        'compensation',
        'pay_ids',
        'contractor_id',

    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function getCompensationFormattedAttribute()
    {
        return '$' . number_format($this->compensation, 2);
    }

}
