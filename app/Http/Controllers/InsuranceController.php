<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Insurance;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function index(Request $request)
    {
        $allInsurances = Insurance::with('contractor')->orderBy('expiration_date')->get();

        $counts = [
            'active' => $allInsurances->where('status', 'active')->count(),
            'expiring_soon' => $allInsurances->where('status', 'expiring_soon')->count(),
            'expired' => $allInsurances->where('status', 'expired')->count(),
        ];

        $activeStatuses = $request->has('filter_applied')
            ? array_intersect($request->input('statuses', []), ['active', 'expiring_soon', 'expired'])
            : ['active', 'expiring_soon', 'expired'];

        $insurances = $allInsurances->whereIn('status', $activeStatuses)->values();
        $contractors = Contractor::all();

        return view('insurances.index', compact('insurances', 'contractors', 'counts', 'activeStatuses'));
    }

    public function create()
    {
        $contractors = Contractor::all();

        return view('insurances.create', compact('contractors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contractor_id' => 'required|string',
            'effective_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:effective_date',
            'link' => 'nullable|url',
        ]);

        Insurance::create($data);

        session()->flash('toast', [
            'type' => 'success',
            'message' => __('Created :name', ['name' => __('Insurance')]),
        ]);

        return redirect()->route('insurances.index');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'contractor_id' => 'required|string',
            'effective_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:effective_date',
            'link' => 'nullable|url',
        ]);

        $insurance = Insurance::findOrFail($id);
        if ($data['expiration_date'] !== optional($insurance->expiration_date)->format('Y-m-d')) {
            $data['notified_at'] = null;
        }
        $insurance->update($data);

        session()->flash('toast', [
            'type' => 'success',
            'message' => __('Updated :name', ['name' => __('Insurance')]),
        ]);

        return redirect()->route('insurances.index');
    }

    public function destroy($id)
    {
        $insurance = Insurance::findOrFail($id);
        $insurance->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => __('Deleted :name', ['name' => __('Insurance')]),
        ]);

        return redirect()->route('insurances.index');
    }
}
