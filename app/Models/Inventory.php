<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Inventory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'inventories';

    protected $fillable = [
        'name',
        'quantity',
        'type',
        'make',
        'equipment_model',
        'asset_tag',
        'serial_number',
        'location',
        'status',
        'downtime_logs',
        'maintenance_events',
        'invoices',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'downtime_logs' => 'array',
            'maintenance_events' => 'array',
            'invoices' => 'array',
        ];
    }

    public function getRevenueTotalAttribute(): float
    {
        return collect($this->invoices ?? [])->sum(fn ($invoice) => (float) ($invoice['amount'] ?? 0));
    }

    public function getRevenueTotalFormattedAttribute(): string
    {
        return '$'.number_format($this->revenue_total, 2);
    }

    public function getDowntimeCountAttribute(): int
    {
        return count($this->downtime_logs ?? []);
    }

    public function getNextMaintenanceAttribute(): ?array
    {
        return collect($this->maintenance_events ?? [])
            ->filter(fn ($event) => ! empty($event['scheduled_at']) && ($event['status'] ?? 'scheduled') !== 'completed')
            ->sortBy('scheduled_at')
            ->first();
    }
}
