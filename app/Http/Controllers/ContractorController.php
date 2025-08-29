<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use Illuminate\Http\Request;

class ContractorController extends Controller
{

    /**
     * Mostrar lista de contratistas.
     */
    public function index(Request $request)
    {

        $query = Contractor::query();

        $contractors = $query->paginate(10);

        return view('contractors.index', compact('contractors'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('contractors.create');
    }

    /**
     * Guardar nuevo contratista.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_name'   => 'required|string|max:255',
            'contact_name'   => 'required|string|max:255',
            'contact_phone'  => 'required|string|max:20',
            'payment_method' => 'required|string|in:Zelle,ACH,Wire'
        ]);

        Contractor::create($request->all());
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Created :name", ['name' => __('Contractor')])
        ]);
        return redirect()->route('contractors.index');
    }



    /**
     * Actualizar contratista.
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'company_name'   => 'required|string|max:255',
            'contact_name'   => 'required|string|max:255',
            'contact_phone'  => 'required|string|max:20',
            'payment_method' => 'required|string|in:Zelle,ACH,Wire'
        ]);

        $contractor = Contractor::findOrFail($id);
        $contractor->update($request->all());
        return redirect()->route('contractors.index');
    }

    /**
     * Eliminar contratista.
     */
    public function destroy($id)
    {
        $contractor = Contractor::findOrFail($id);
        $contractor->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Deleted :name", ['name' => __('Contractor')])
        ]);

        return redirect()->route('contractors.index');
    }
}
