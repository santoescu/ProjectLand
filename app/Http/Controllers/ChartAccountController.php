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

        $chartAccounts = $query->paginate(100);

        return view('chartAccounts.index', compact('chartAccounts'));
    }

    public function tree()
    {
        $rootAccounts = ChartAccount::whereNull('parent_id')->orWhere('parent_id', '')->get();

        return view('chartAccounts.tree', compact( 'rootAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $chartAccounts = ChartAccount::all();
        return view('chartAccounts.create', compact('chartAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
        ]);


        $data = $request->all();
        $data['parent_id'] = $data['parent_id'] ?? "";
        ChartAccount::create($data);
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
            'parent_id' => 'nullable|not_in:'. $id,
        ]);

        $chartAccount = ChartAccount::findOrFail($id);
        $data = $request->all();
        $data['parent_id'] = $data['parent_id'] ?? "";
        $chartAccount->update($data);
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
