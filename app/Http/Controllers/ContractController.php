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

        $contracts = $query->with('contractor', 'project')->get();
        $contractors = Contractor::all();
        $projects = Project::activeOrId($effectiveProjectId)->orderBy('name')->get();
        $chartAccounts = ChartAccount::all();
        return view('contracts.index', compact('contracts', 'contractors', 'projects', 'chartAccounts', 'selectedProject', 'effectiveProjectId'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        if ($this->selectedProjectIsInactive()) {
            session()->flash('toast', [
                'type' => 'warning',
                'message' => __('Inactive projects cannot be used to create new records.'),
            ]);

            return redirect()->route('contracts.index');
        }

        $contractors = Contractor::all();
        $projects = Project::active()->orderBy('name')->get();
        $chartAccounts = ChartAccount::all();
        $selectedProject = session('selected_project');
        $effectiveProjectId = data_get($selectedProject, 'id');
        return view('contracts.create', compact('contractors', 'projects', 'chartAccounts', 'selectedProject', 'effectiveProjectId'));
    }

    public function show($id)
    {
        $contract = Contract::with('contractor', 'project')->findOrFail($id);
        $detailData = $this->paymentDetailData($contract);

        return view('contracts.show', array_merge(['contract' => $contract], $detailData));
    }

    public function paymentDetailTable($id)
    {
        $contract = Contract::with('contractor', 'project')->findOrFail($id);
        $detailData = $this->paymentDetailData($contract);

        return view('contracts.partials.payment-detail-table', array_merge(['contract' => $contract], $detailData));
    }

    private function paymentDetailData(Contract $contract): array
    {
        $chartAccounts = ChartAccount::all()->keyBy(fn ($chartAccount) => (string) $chartAccount->_id);
        $payments = Pay::where('contract_id', (string) $contract->_id)
            ->where('status', 2)
            ->orderBy('created_at')
            ->get()
            ->map(function ($pay) {
                $paidHistory = collect($pay->histories ?? [])->first(function ($history) {
                    return in_array($history['action'] ?? '', ['Paid', __('Paid')], true);
                });

                $pay->paid_at = data_get($paidHistory, 'created_at') ?? $pay->updated_at ?? $pay->created_at;

                return $pay;
            })
            ->sortBy('paid_at')
            ->values();

        $budgetRows = collect($contract->contract_budgets ?? [])->map(function ($budget) use ($chartAccounts, $payments) {
            $chartAccountId = (string) ($budget['chartAccount_id'] ?? '');
            $concept = trim((string) ($budget['concept'] ?? ''));
            $budgetKey = $this->budgetKey($chartAccountId, $concept);
            $paymentAmounts = $payments->mapWithKeys(function ($pay) use ($chartAccountId, $budgetKey) {
                $allocations = collect($pay->payment_allocations ?? []);
                $amount = $allocations->isNotEmpty()
                    ? $allocations->sum(function ($allocation) use ($budgetKey) {
                        $allocationKey = (string) ($allocation['budget_key'] ?? '') ?: $this->budgetKey(
                            (string) ($allocation['chartAccount_id'] ?? ''),
                            (string) ($allocation['concept'] ?? '')
                        );

                        return $allocationKey === $budgetKey ? (float) ($allocation['amount'] ?? 0) : 0;
                    })
                    : ((string) ($pay->chartAccount_id ?? '') === $chartAccountId ? (float) ($pay->amount ?? 0) : 0);

                return [(string) $pay->_id => $amount];
            });

            return [
                'chartAccount_id' => $chartAccountId,
                'budget_key' => $budgetKey,
                'chartAccount_name' => $chartAccounts->get($chartAccountId)?->name ?? '',
                'concept' => $concept,
                'budget' => (float) ($budget['budget'] ?? 0),
                'spent' => $paymentAmounts->sum(),
                'remaining' => max(((float) ($budget['budget'] ?? 0)) - $paymentAmounts->sum(), 0),
                'payments' => $paymentAmounts,
            ];
        })->values();

        return compact('payments', 'budgetRows');
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
            'contractor_id' => 'required|string',
            'project_id' => 'required|string',
            'subproject' => 'nullable|string|max:255',
            'contract_budgets' => 'required|array|min:1',
            'contract_budgets.*.chartAccount_id' => 'required|string',
            'contract_budgets.*.budget' => 'required|numeric|min:0',
            'contract_budgets.*.concept' => 'nullable|string|max:255',
        ]);

        if (!Project::active()->find($data['project_id'])) {
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

        $duplicateAccountIds = collect($data['contract_budgets'])
            ->groupBy(fn ($budget) => (string) ($budget['chartAccount_id'] ?? ''))
            ->filter(fn ($budgets, $chartAccountId) => filled($chartAccountId) && $budgets->count() > 1)
            ->keys();

        if ($duplicateAccountIds->isNotEmpty()) {
            foreach ($data['contract_budgets'] as $budget) {
                $chartAccountId = (string) ($budget['chartAccount_id'] ?? '');

                if ($duplicateAccountIds->contains($chartAccountId) && blank($budget['concept'] ?? null)) {
                    throw ValidationException::withMessages([
                        'contract_budgets' => __('The concept field is required when the budget code is repeated.'),
                    ]);
                }
            }

            $repeatedKeys = collect($data['contract_budgets'])
                ->map(fn ($budget) => (string) ($budget['chartAccount_id'] ?? '') . '|' . strtolower(trim((string) ($budget['concept'] ?? ''))));

            if ($repeatedKeys->duplicates()->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'contract_budgets' => __('The same budget code cannot use the same concept more than once.'),
                ]);
            }
        }

        $data['contract_budgets'] = collect($data['contract_budgets'])
            ->map(fn ($budget) => [
                'chartAccount_id' => (string) $budget['chartAccount_id'],
                'budget' => (float) $budget['budget'],
                'concept' => trim((string) ($budget['concept'] ?? '')),
            ])
            ->values()
            ->all();
        $data['compensation'] = collect($data['contract_budgets'])->sum('budget');

        return $data;
    }

    private function selectedProjectIsInactive(): bool
    {
        $selectedProjectId = data_get(session('selected_project'), 'id');

        if (blank($selectedProjectId)) {
            return false;
        }

        $project = Project::find($selectedProjectId);

        return $project && !$project->is_active;
    }

    private function syncContractBudgetUsage(string $contractId): void
    {
        $contract = Contract::find($contractId);

        if (!$contract) {
            return;
        }

        $budgets = collect($contract->contract_budgets ?? [])->map(function ($budget) use ($contract) {
            $chartAccountId = (string) ($budget['chartAccount_id'] ?? '');
            $concept = trim((string) ($budget['concept'] ?? ''));
            $budgetKey = $this->budgetKey($chartAccountId, $concept);
            $budgetAmount = (float) ($budget['budget'] ?? 0);
            $spent = $this->spentForContractBudget((string) $contract->_id, $chartAccountId, $budgetKey);

            return [
                'chartAccount_id' => $chartAccountId,
                'budget_key' => $budgetKey,
                'budget' => $budgetAmount,
                'spent' => $spent,
                'remaining' => max($budgetAmount - $spent, 0),
                'concept' => $concept,
            ];
        })->values()->all();

        $contract->update(['contract_budgets' => $budgets]);
    }

    private function spentForContractBudget(string $contractId, string $chartAccountId, ?string $budgetKey = null): float
    {
        return Pay::where('contract_id', $contractId)
            ->where('status', '!=', 1)
            ->get()
            ->sum(function ($pay) use ($chartAccountId, $budgetKey) {
                $allocations = collect($pay->payment_allocations ?? []);

                if ($allocations->isNotEmpty()) {
                    return $allocations->sum(function ($allocation) use ($chartAccountId, $budgetKey) {
                        if ($budgetKey) {
                            $allocationKey = (string) ($allocation['budget_key'] ?? '') ?: $this->budgetKey(
                                (string) ($allocation['chartAccount_id'] ?? ''),
                                (string) ($allocation['concept'] ?? '')
                            );

                            return $allocationKey === $budgetKey ? (float) ($allocation['amount'] ?? 0) : 0;
                        }

                        return (string) ($allocation['chartAccount_id'] ?? '') === $chartAccountId ? (float) ($allocation['amount'] ?? 0) : 0;
                    });
                }

                return (string) ($pay->chartAccount_id ?? '') === $chartAccountId ? (float) ($pay->amount ?? 0) : 0;
            });
    }

    private function budgetKey(string $chartAccountId, string $concept = ''): string
    {
        return $chartAccountId . '|' . strtolower(trim($concept));
    }
}
