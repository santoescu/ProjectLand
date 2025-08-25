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
        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Guardado correctamente'
        ]);

        $query = Contractor::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('company_name', 'like', "%{$search}%")
                ->orWhere('contact_name', 'like', "%{$search}%");
        }

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
        return redirect()->route('contractors.index')
            ->with('success', 'Contratista creado correctamente.');
    }

    /**
     * Mostrar un contratista específico.
     */
    public function show($id)
    {
        $contractor = Contractor::findOrFail($id);
        return view('contractors.show', compact('contractor'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit($id)
    {
        $contractor = Contractor::findOrFail($id);
        return view('contractors.edit', compact('contractor'));
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

        return redirect()->route('contractors.index')
            ->with('success', 'Contratista actualizado correctamente.');
    }

    /**
     * Eliminar contratista.
     */
    public function destroy($id)
    {
        $contractor = Contractor::findOrFail($id);
        $contractor->delete();

        return redirect()->route('contractors.index')
            ->with('success', 'Contratista eliminado correctamente.');
    }
}
