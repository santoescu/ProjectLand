<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Pay extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'pays';

    protected $fillable = [
        'project_id',
        'subproject',
        'contractor_id',
        'chartAccount_id',
        'amount',
        'description',
        'status',
        'user_id',
        'histories'
    ];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function chartAccount()
    {
        return $this->belongsTo(ChartAccount::class, 'chartAccount_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            0 => 'yellow',
            1 => 'red',
            2 => 'green',
            3 => 'blue',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            0 => __("Pending"),
            1 => __("Rejected"),
            2 => __("Paid"),
            3 => __("Approved"),
            default => __("Unknown"),
        };
    }
    public function getAmountFormattedAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }
}
