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

        $projects = $query->get();

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

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'subprojects' => 'nullable|array',
            'subprojects.*' => 'nullable|string|max:255',
        ]);

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

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'subprojects' => 'nullable|array',
            'subprojects.*' => 'nullable|string|max:255',
        ]);

        $project = Project::findOrFail($id);


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
