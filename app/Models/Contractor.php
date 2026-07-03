<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Contractor extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'contractors';

    protected $fillable = [
        'company_name',
        'contact_name',
        'contact_phone',
        'contact_email',
        'payment_method',
    ];

    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }
}
