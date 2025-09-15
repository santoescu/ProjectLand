<?php

namespace App\Http\Controllers;
use App\Models\ChartAccount;
use Illuminate\Http\Request;

class ChartAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ChartAccount::query();

        $chartAccounts = $query->paginate(10);

        return view('chartAccounts.index', compact('chartAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('chartAccounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'type'   => 'required|string|max:255',
        ]);

        ChartAccount::create($request->all());
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Created :name", ['name' => __('Chart of Account')])
        ]);
        return redirect()->route('chartAccounts.index');
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'type'   => 'required|string|max:255',
        ]);

        $chartAccount = ChartAccount::findOrFail($id);
        $chartAccount->update($request->all());
        return redirect()->route('chartAccounts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $chartAccount = ChartAccount::findOrFail($id);
        $chartAccount->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Deleted :name", ['name' => __('Chart of Account')])
        ]);

        return redirect()->route('chartAccounts.index');
    }
}
