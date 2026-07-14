<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Project;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * Mostrar los portfolios creados.
     */
    public function index()
    {
        $portfolios = Portfolio::query()->get();

        $projects = Project::query()
            ->whereIn('_id', $portfolios->map(fn ($portfolio) => (string) $portfolio->project_id)->unique()->all())
            ->get()
            ->keyBy(fn ($project) => (string) $project->id);

        $rows = $portfolios
            ->map(fn ($portfolio) => [
                'portfolio' => $portfolio,
                'project' => $projects->get((string) $portfolio->project_id),
            ])
            ->sortBy(fn ($row) => $row['project']->name ?? '')
            ->values();

        $selectableProjects = Project::query()->active()->orderBy('name')->get();

        return view('portfolio', compact('rows', 'selectableProjects'));
    }

    /**
     * Crear un nuevo portfolio asociado a un proyecto existente.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|string',
        ]);

        Project::findOrFail($data['project_id']);

        Portfolio::create([
            'project_id' => $data['project_id'],
            'phase' => '',
            'schedule_percent' => 0,
            'original_budget' => 0,
            'revised_budget' => 0,
            'spent_to_date' => 0,
            'milestones' => [],
        ]);

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Created :name", ['name' => __('Portfolio')]),
        ]);

        return redirect()->route('portfolio');
    }

    /**
     * Actualizar los datos de un portfolio.
     */
    public function update(Request $request, $portfolioId)
    {
        $data = $request->validate([
            'phase' => 'nullable|string|max:255',
            'schedule_percent' => 'nullable|numeric|min:0|max:100',
            'original_budget' => 'nullable|numeric|min:0',
            'revised_budget' => 'nullable|numeric|min:0',
            'spent_to_date' => 'nullable|numeric|min:0',
            'milestones' => 'nullable|array',
            'milestones.*.name' => 'required|string|max:255',
            'milestones.*.target' => 'nullable|date',
            'milestones.*.actual' => 'nullable|date',
            'milestones.*.status' => 'required|string|in:pending,current,done',
        ]);

        if (array_key_exists('milestones', $data)) {
            $data['milestones'] = array_values($data['milestones']);
        }

        $portfolio = Portfolio::findOrFail($portfolioId);
        $portfolio->update($data);

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Updated :name", ['name' => __('Portfolio')]),
        ]);

        return redirect()->route('portfolio');
    }

    /**
     * Actualizar el estado de un milestone puntual.
     */
    public function updateMilestoneStatus(Request $request, $portfolioId, $index)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,current,done',
        ]);

        $portfolio = Portfolio::findOrFail($portfolioId);
        $milestones = $portfolio->milestones;

        abort_unless(array_key_exists((int) $index, $milestones), 404);

        $milestones[(int) $index]['status'] = $data['status'];
        $portfolio->update(['milestones' => $milestones]);

        return redirect()->route('portfolio');
    }
}
