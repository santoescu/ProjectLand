<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Contractor;
use Illuminate\Http\Request;

class ContractController extends Controller
{

    /**
     * Mostrar lista de contratos.
     */
    public function index(Request $request)
    {

        $query = Contract::query();

        $contracts = $query->paginate(10);
        $contractors = Contractor::all();
        return view('contracts.index', compact('contracts', 'contractors'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $contractors = Contractor::all();
        return view('contracts.create', compact('contractors'));
    }

    /**
     * Guardar nuevo contratista.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'compensation'   => 'required|numeric|min:0',
            'contractor_id' => 'required|string',
            ]);

        Contract::create($request->all());
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Created :name", ['name' => __('Contract')]),
        ]);
        return redirect()->route('contracts.index');
    }



    /**
     * Actualizar contrato.
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'name'   => 'required|string|max:255',
            'compensation'   => 'required|numeric|min:0',
            'contractor_id' => 'required|string',
        ]);

        $contract = Contract::findOrFail($id);
        $contract->update($request->all());
        return redirect()->route('contracts.index');
    }

    /**
     * Eliminar contrato.
     */
    public function destroy($id)
    {
        $contractor = Contract::findOrFail($id);
        $contractor->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Deleted :name", ['name' => __('Contract')]),
        ]);

        return redirect()->route('contracts.index');
    }
}
