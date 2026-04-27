<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Mostrar lista de proyecto.
     */
    public function index(Request $request)
    {

        $query = Project::query();

        $projects = $query->paginate(10);

        return view('projects.index', compact('projects'));
    }

    /**
     * Mostrar formulario de proyecto.
     */
    public function create()
    {
        $projects = Project::all();
        return view('projects.create', compact('projects'));
    }

    /**
     * Guardar nuevo proyecto.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name'   => 'required|string|max:255',
            'subprojects' => 'nullable|array',
            'subprojects.*' => 'nullable|string|max:255',
        ]);
        $data = $request->all();

        if ($request->has('subprojects')) {

            $data['subprojects'] = collect($data['subprojects'])
                ->filter(fn($item) => !is_null($item) && trim($item) !== '')
                ->values()
                ->toArray();
        }
        Project::create($data);
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Created :name", ['name' => __('Project')]),
        ]);
        return redirect()->route('projects.index');
    }



    /**
     * Actualizar proyecto.
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'name'   => 'required|string|max:255',
            'subprojects' => 'nullable|array',
            'subprojects.*' => 'nullable|string|max:255',
        ]);

        $project = Project::findOrFail($id);
        $data = $request->all();


        if ($request->has('subprojects')) {

            $data['subprojects'] = collect($data['subprojects'])
                ->filter(fn($item) => !is_null($item) && trim($item) !== '')
                ->values()
                ->toArray();
        }
        $project->update($data);
        return redirect()->route('projects.index');
    }

    /**
     * Eliminar proyecto.
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Deleted :name", ['name' => __('Projects')])
        ]);

        return redirect()->route('projects.index');
    }
}
