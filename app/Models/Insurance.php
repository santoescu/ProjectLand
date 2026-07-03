<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use MongoDB\Laravel\Eloquent\Model;

class Insurance extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'insurances';

    protected $fillable = [
        'contractor_id',
        'effective_date',
        'expiration_date',
        'link',
        'notified_at',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiration_date' => 'date',
        'notified_at' => 'datetime',
    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function getStatusAttribute()
    {
        if (!$this->expiration_date) {
            return 'unknown';
        }

        $today = Carbon::today();
        $expiration = Carbon::parse($this->expiration_date);

        if ($expiration->lt($today)) {
            return 'expired';
        }

        if ($expiration->lte($today->copy()->addDays(30))) {
            return 'expiring_soon';
        }

        return 'active';
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'green',
            'expiring_soon' => 'yellow',
            'expired' => 'red',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => __('Active'),
            'expiring_soon' => __('Expiring Soon'),
            'expired' => __('Expired'),
            default => __('Unknown'),
        };
    }
}
