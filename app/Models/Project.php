<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Project extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'projects';

    protected $fillable = [
        'name',
        'image',
        'subprojects',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->where('status', 'active')
                ->orWhereNull('status')
                ->orWhere('status', '');
        });
    }

    public function scopeActiveOrId($query, $projectId)
    {
        return $query->where(function ($query) use ($projectId) {
            $query->active();

            if (filled($projectId)) {
                $query->orWhere('_id', (string) $projectId);
            }
        });
    }

    public function getStatusAttribute($value)
    {
        return $value ?: 'active';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? __('Active') : __('Inactive');
    }

    public function getStatusBadgeClassesAttribute(): string
    {
        return $this->is_active
            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
            : 'bg-gray-100 text-gray-700 dark:bg-neutral-700 dark:text-neutral-300';
    }

    public function getNameSubProjectAttribute()
    {

        if (empty($this->subprojects) || !is_array($this->subprojects)) {
            return '';
        }

        return implode('; ', $this->subprojects);
    }
}
