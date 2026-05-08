<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Contractor;
use App\Models\ChartAccount;
use App\Models\Pay;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContractController extends Controller
{

    /**
     * Mostrar lista de contratos.
     */
    public function index(Request $request)
    {

        $selectedProject = session('selected_project');
        $effectiveProjectId = data_get($selectedProject, 'id');

        $query = Contract::query();

        if (filled($effectiveProjectId)) {
            $query->where('project_id', $effectiveProjectId);
        }

        $contracts = $query->with('contractor', 'project')->paginate(10);
        $contractors = Contractor::all();
        $projects = Project::all();
        $chartAccounts = ChartAccount::all();
        return view('contracts.index', compact('contracts', 'contractors', 'projects', 'chartAccounts', 'selectedProject', 'effectiveProjectId'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $contractors = Contractor::all();
        $projects = Project::all();
        $chartAccounts = ChartAccount::all();
        $selectedProject = session('selected_project');
        $effectiveProjectId = data_get($selectedProject, 'id');
        return view('contracts.create', compact('contractors', 'projects', 'chartAccounts', 'selectedProject', 'effectiveProjectId'));
    }

    /**
     * Guardar nuevo contratista.
     */
    public function store(Request $request)
    {
        $contract = Contract::create($this->validatedContractData($request));
        $this->syncContractBudgetUsage((string) $contract->_id);
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

        $contract = Contract::findOrFail($id);
        $contract->update($this->validatedContractData($request));
        $this->syncContractBudgetUsage((string) $contract->_id);
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

    private function validatedContractData(Request $request): array
    {
        $selectedProjectId = data_get(session('selected_project'), 'id');

        if (filled($selectedProjectId)) {
            $request->merge(['project_id' => $selectedProjectId]);
        }

        $budgets = collect($request->input('contract_budgets', []))
            ->filter(fn ($budget) => filled($budget['chartAccount_id'] ?? null) || filled($budget['budget'] ?? null))
            ->values()
            ->all();

        $request->merge(['contract_budgets' => $budgets]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'compensation' => 'required|numeric|min:0',
            'contractor_id' => 'required|string',
            'project_id' => 'required|string',
            'contract_budgets' => 'required|array|min:1',
            'contract_budgets.*.chartAccount_id' => 'required|string|distinct',
            'contract_budgets.*.budget' => 'required|numeric|min:0',
        ]);

        if (!Project::find($data['project_id'])) {
            throw ValidationException::withMessages([
                'project_id' => __('The selected project is invalid.'),
            ]);
        }

        foreach ($data['contract_budgets'] as $budget) {
            if (!ChartAccount::find($budget['chartAccount_id'])) {
                throw ValidationException::withMessages([
                    'contract_budgets' => __('One of the selected budget codes is invalid.'),
                ]);
            }
        }

        $data['compensation'] = (float) $data['compensation'];
        $data['contract_budgets'] = collect($data['contract_budgets'])
            ->map(fn ($budget) => [
                'chartAccount_id' => (string) $budget['chartAccount_id'],
                'budget' => (float) $budget['budget'],
            ])
            ->values()
            ->all();

        return $data;
    }

    private function syncContractBudgetUsage(string $contractId): void
    {
        $contract = Contract::find($contractId);

        if (!$contract) {
            return;
        }

        $budgets = collect($contract->contract_budgets ?? [])->map(function ($budget) use ($contract) {
            $chartAccountId = (string) ($budget['chartAccount_id'] ?? '');
            $budgetAmount = (float) ($budget['budget'] ?? 0);
            $spent = $this->spentForContractBudget((string) $contract->_id, $chartAccountId);

            return [
                'chartAccount_id' => $chartAccountId,
                'budget' => $budgetAmount,
                'spent' => $spent,
                'remaining' => max($budgetAmount - $spent, 0),
            ];
        })->values()->all();

        $contract->update(['contract_budgets' => $budgets]);
    }

    private function spentForContractBudget(string $contractId, string $chartAccountId): float
    {
        return Pay::where('contract_id', $contractId)
            ->where('status', '!=', 1)
            ->get()
            ->sum(function ($pay) use ($chartAccountId) {
                $allocations = collect($pay->payment_allocations ?? []);

                if ($allocations->isNotEmpty()) {
                    return $allocations
                        ->where('chartAccount_id', $chartAccountId)
                        ->sum(fn ($allocation) => (float) ($allocation['amount'] ?? 0));
                }

                return (string) ($pay->chartAccount_id ?? '') === $chartAccountId ? (float) ($pay->amount ?? 0) : 0;
            });
    }
}
