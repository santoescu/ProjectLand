<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('inventories.index', compact('inventories'));
    }

    public function create()
    {
        return view('inventories.create');
    }

    public function store(Request $request)
    {
        Inventory::create($this->validatedData($request));

        session()->flash('toast', [
            'type' => 'success',
            'message' => __('Created :name', ['name' => __('Equipment')]),
        ]);

        return redirect()->route('inventories.index');
    }

    public function edit(Inventory $inventory)
    {
        return view('inventories.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $inventory->update($this->validatedData($request));

        session()->flash('toast', [
            'type' => 'success',
            'message' => __('Updated :name', ['name' => __('Equipment')]),
        ]);

        return redirect()->route('inventories.index');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => __('Deleted :name', ['name' => __('Equipment')]),
        ]);

        return redirect()->route('inventories.index');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'string', 'max:255'],
            'make' => ['nullable', 'string', 'max:255'],
            'equipment_model' => ['nullable', 'string', 'max:255'],
            'asset_tag' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive,maintenance,retired'],
            'notes' => ['nullable', 'string'],
            'downtime_logs' => ['nullable', 'array'],
            'downtime_logs.*.started_at' => ['nullable', 'date'],
            'downtime_logs.*.ended_at' => ['nullable', 'date'],
            'downtime_logs.*.reason' => ['nullable', 'string', 'max:255'],
            'downtime_logs.*.notes' => ['nullable', 'string'],
            'maintenance_events' => ['nullable', 'array'],
            'maintenance_events.*.scheduled_at' => ['nullable', 'date'],
            'maintenance_events.*.completed_at' => ['nullable', 'date'],
            'maintenance_events.*.description' => ['nullable', 'string', 'max:255'],
            'maintenance_events.*.technician' => ['nullable', 'string', 'max:255'],
            'maintenance_events.*.status' => ['nullable', 'string', 'in:scheduled,in_progress,completed,cancelled'],
            'invoices' => ['nullable', 'array'],
            'invoices.*.number' => ['nullable', 'string', 'max:255'],
            'invoices.*.date' => ['nullable', 'date'],
            'invoices.*.customer' => ['nullable', 'string', 'max:255'],
            'invoices.*.amount' => ['nullable', 'numeric', 'min:0'],
            'invoices.*.description' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['downtime_logs'] = $this->cleanRows($validated['downtime_logs'] ?? [], ['started_at', 'ended_at', 'reason', 'notes']);
        $validated['maintenance_events'] = $this->cleanRows($validated['maintenance_events'] ?? [], ['scheduled_at', 'completed_at', 'description', 'technician', 'status']);
        $validated['invoices'] = $this->cleanRows($validated['invoices'] ?? [], ['number', 'date', 'customer', 'amount', 'description']);

        return $validated;
    }

    private function cleanRows(array $rows, array $keys): array
    {
        return collect($rows)
            ->map(fn ($row) => collect($keys)->mapWithKeys(fn ($key) => [$key => $row[$key] ?? null])->toArray())
            ->filter(fn ($row) => collect($row)->filter(fn ($value) => filled($value))->isNotEmpty())
            ->values()
            ->toArray();
    }
}
