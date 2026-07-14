<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Portfolio extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'portfolios';

    protected $fillable = [
        'project_id',
        'phase',
        'schedule_percent',
        'original_budget',
        'revised_budget',
        'spent_to_date',
        'milestones',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function getPhaseAttribute($value)
    {
        return $value ?: '';
    }

    public function getSchedulePercentAttribute($value)
    {
        return (float) ($value ?? 0);
    }

    public function getOriginalBudgetAttribute($value)
    {
        return (float) ($value ?? 0);
    }

    public function getRevisedBudgetAttribute($value)
    {
        return (float) ($value ?? 0);
    }

    public function getSpentToDateAttribute($value)
    {
        return (float) ($value ?? 0);
    }

    public function getMilestonesAttribute($value)
    {
        return is_array($value) ? $value : [];
    }

    public function getBudgetCompletePercentAttribute(): float
    {
        if ($this->revised_budget <= 0) {
            return 0;
        }

        return round(($this->spent_to_date / $this->revised_budget) * 100, 2);
    }

    public function getVarianceAttribute(): float
    {
        return $this->revised_budget - $this->original_budget;
    }

    public function getBalanceToCompleteAttribute(): float
    {
        return $this->revised_budget - $this->spent_to_date;
    }
}
