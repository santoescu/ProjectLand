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
        'change_order_budgets',
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
        $value = (float) $this->compensation;
        if ($value < 0) {
            return '-$' . number_format(abs($value), 2, '.', ',');
        }
        return '$' . number_format($value, 2, '.', ',');
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
        $contractRemaining = collect($this->contract_budgets ?? [])->sum(function ($budget) {
            return array_key_exists('remaining', $budget)
                ? (float) ($budget['remaining'] ?? 0)
                : (float) ($budget['budget'] ?? 0);
        });

        $coRemaining = collect($this->change_order_budgets ?? [])->sum(function ($budget) {
            return array_key_exists('remaining', $budget)
                ? (float) ($budget['remaining'] ?? 0)
                : (float) ($budget['budget'] ?? 0);
        });

        return $contractRemaining + $coRemaining;
    }

    public function getRemainingBudgetTotalFormattedAttribute()
    {
        $value = $this->remaining_budget_total;
        if ($value < 0) {
            return '-$' . number_format(abs($value), 2, '.', ',');
        }
        return '$' . number_format($value, 2, '.', ',');
    }

    public function getChangeOrderTotalAttribute()
    {
        return collect($this->change_order_budgets ?? [])->sum(fn ($budget) => (float) ($budget['budget'] ?? 0));
    }

    public function getChangeOrderTotalFormattedAttribute()
    {
        $value = $this->change_order_total;
        if ($value < 0) {
            return '-$' . number_format(abs($value), 2, '.', ',');
        }
        return '$' . number_format($value, 2, '.', ',');
    }

}
