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
        'project_id',
        'subproject',
        'contract_budgets',

    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getCompensationFormattedAttribute()
    {
        return '$' . number_format($this->compensation, 2, '.', ',');
    }

    public function getBudgetTotalAttribute()
    {
        return collect($this->contract_budgets ?? [])->sum(fn ($budget) => (float) ($budget['budget'] ?? 0));
    }

    public function getBudgetTotalFormattedAttribute()
    {
        return '$' . number_format($this->budget_total, 2, '.', ',');
    }

    public function getRemainingBudgetTotalAttribute()
    {
        return collect($this->contract_budgets ?? [])->sum(function ($budget) {
            return array_key_exists('remaining', $budget)
                ? (float) ($budget['remaining'] ?? 0)
                : (float) ($budget['budget'] ?? 0);
        });
    }

    public function getRemainingBudgetTotalFormattedAttribute()
    {
        return '$' . number_format($this->remaining_budget_total, 2, '.', ',');
    }

}
