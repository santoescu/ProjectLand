<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use App\Models\Contract;
use App\Models\Contractor;
use App\Models\Pay;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class PayController extends Controller
{
    public function index(Request $request)
    {
        $selectedProject = session('selected_project');
        $effectiveProjectId = data_get($selectedProject, 'id') ?: $request->project_id;

        $query = Pay::with('project', 'contractor', 'chartAccount');

        if (!is_null($request->status) && $request->status !== '') {
            $query->where('status', (int) $request->status);
        }
        if (!is_null($effectiveProjectId) && $effectiveProjectId !== '') {
            $query->where('project_id', $effectiveProjectId);
        }


        $pays = $query->orderBy('created_at', 'desc')->get();
        $projects = Project::active()->orderBy('name')->get();

        return view('pays.index', compact('projects', 'pays', 'selectedProject', 'effectiveProjectId'));
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

            return redirect()->route('pays.index');
        }

        $chartAccounts = ChartAccount::all();
        $contracts = Contract::all();
        $contractsForFront = $this->contractsForFront();
        $projects = Project::active()->orderBy('name')->get();
        $contractors = Contractor::all();
        $selectedProject = session('selected_project');
        $effectiveProjectId = data_get($selectedProject, 'id');
        return view('pays.create', compact('chartAccounts', 'projects', 'contractors', 'contracts', 'contractsForFront', 'selectedProject', 'effectiveProjectId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|string',
            'subproject' => 'nullable|string|max:255',
            'contractor_id' => 'required|string',
            'contract_id' => 'nullable|string',
            'chartAccount_id' => 'nullable|string',
            'payment_allocations' => 'nullable|array',
            'payment_allocations.*.chartAccount_id' => 'nullable|string|distinct',
            'payment_allocations.*.amount' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'attachment_link' => 'nullable|url',
        ]);

        $data = $this->applySelectedProject($request->all());
        $this->ensureActiveProject($data['project_id'] ?? null);
        $data = $this->preparePaymentBudgetData($data);
        $data['contract_id'] = $this->validateContractSelection(
            $data['contractor_id'] ?? null,
            $data['project_id'] ?? null,
            $data['chartAccount_id'] ?? null,
            $data['contract_id'] ?? null
        );
        $data['amount'] = (float) $data['amount'];
        $users = User::whereIn('role', ['director', 'admin'])->get();
        $data['user_id'] = $users->pluck('_id')->toArray();;
        $data['status'] = 0;
        $data['histories'] = [
            [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'Created',
                'created_at' => now()
            ]
        ];

        $pay = Pay::create($data);
        $this->syncPayContract(null, $pay->contract_id ?? null, (string) $pay->_id);
        $this->syncContractBudgetUsage($pay->contract_id ?? null);

        try {
            foreach ($users as $user) {
                $emailData = [
                    'pay' => $pay,
                    'user' => $user, // importante: pasamos el usuario al email
                ];

                Mail::send('emails.pays', $emailData, function ($message) use ($user, $pay) {
                    $message->to($user->email)
                        ->subject($pay->project->name.' - '.$pay->contractor->company_name.' - '.'New Payment Request');
                });
            }
        } catch (Exception $e) {

        }



        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Created :name", ['name' => __('Payable')]),
        ]);
        return redirect()->route('pays.index');
    }

    public function update(Request $request, $id)
    {
        $pay = Pay::findOrFail($id);
        $request->validate([
            'project_id' => 'nullable|string',
            'subproject' => 'nullable|string|max:255',
            'contractor_id' => 'nullable|string',
            'contract_id' => 'nullable|string',
            'chartAccount_id' => 'nullable|string',
            'payment_allocations' => 'nullable|array',
            'payment_allocations.*.chartAccount_id' => 'nullable|string|distinct',
            'payment_allocations.*.amount' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'attachment_link' => 'nullable|url',
        ]);

        $data = $this->applySelectedProject($request->all());
        if (empty($data['project_id'])) {
            $data['project_id'] = $pay->project_id;
        }
        $this->ensureActiveProject($data['project_id'] ?? null, $pay->project_id ?? null);

        if (empty($data['contractor_id'])) {
            $data['contractor_id'] = $pay->contractor_id;
        }

        if (empty($data['contract_id'])) {
            $data['contract_id'] = null;
        }

        if (empty($data['chartAccount_id'])) {
            $data['chartAccount_id'] = $pay->chartAccount_id;
        }

        $data = $this->preparePaymentBudgetData($data, (string) $pay->_id);

        $data['contract_id'] = $this->validateContractSelection(
            $data['contractor_id'] ?? null,
            $data['project_id'] ?? null,
            $data['chartAccount_id'] ?? null,
            $data['contract_id']
        );

        $data['amount'] = (float) $data['amount'];
        $newHistory =
            [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'Updated',
                'created_at' => now()
            ];


        $existingHistories = $pay->histories ?? [];
        $data['histories'] = array_merge($existingHistories, [$newHistory]);
        $previousContractId = $pay->contract_id ?? null;
        $pay->update($data);
        $this->syncPayContract($previousContractId, $pay->contract_id ?? null, (string) $pay->_id);
        $this->syncContractBudgetUsage($previousContractId);
        $this->syncContractBudgetUsage($pay->contract_id ?? null);
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Updated :name", ['name' => __('Payable')]),
        ]);
        return redirect()->route('pays.index');
    }
    public function edit($id)
    {

        $pay= Pay::findOrFail($id);

        $chartAccounts = ChartAccount::all();
        $contracts = Contract::all();
        $contractsForFront = $this->contractsForFront((string) $pay->_id);
        $projects = Project::activeOrId($pay->project_id ?? null)->orderBy('name')->get();
        $contractors = Contractor::all();
        $selectedProject = session('selected_project');
        $effectiveProjectId = data_get($selectedProject, 'id');
        return view('pays.edit', compact('pay','chartAccounts', 'projects', 'contractors', 'contracts', 'contractsForFront', 'selectedProject', 'effectiveProjectId'));
    }

    public function updateStatus($id, $status,$user_id)
    {
        $pay = Pay::findOrFail($id);
        $contractId = $pay->contract_id ?? null;

        // Actualizar el estado
        $pay->status = (int) $status;

        $action = match((int) $status) {
            0 => __("Pending"),
            1 => __("Rejected"),
            2 => __("Paid"),
            3 => __("Approved"),
            default => 'status_changed',
        };
        $user = User::findOrFail($user_id);
        // Agregar historial sin borrar los anteriores
        $histories = $pay->histories ?? [];
        $users = User::where('role', 'accounting_assistant')->get();
        $histories[] = [
            'user_id' => $user->_id,
            'user_name' => $user->name,
            'action' => $action,
            'created_at' => now(),
        ];
        $pay->user_id= [];
        $pay->histories = $histories;
        $pay->save();
        if($status==3){
            $pay->user_id= $users->pluck('_id')->toArray();
            $pay->save();
            try {
                foreach ($users as $user) {
                    $emailData = [
                        'pay' => $pay,
                        'user' => $user, // importante: pasamos el usuario al email
                    ];

                    Mail::send('emails.pays', $emailData, function ($message) use ($user, $pay) {
                        $message->to($user->email)
                            ->subject($pay->project->name.' - '.$pay->contractor->company_name.' - '.'New Payment Request');
                    });
                }
            } catch (Exception $e) {

            }
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Updated :name", ['name' => __('Payable')]),
        ]);
        $this->syncContractBudgetUsage($contractId);
        return back();
    }

    public function updatePay($id, $user_id)
    {

        $pay= Pay::findOrFail($id);
        $user = User::findOrFail($user_id);

        $chartAccounts = ChartAccount::all();
        $contracts = Contract::all();
        $contractsForFront = $this->contractsForFront((string) $pay->_id);
        $projects = Project::activeOrId($pay->project_id ?? null)->orderBy('name')->get();
        $contractors = Contractor::all();
        $selectedProject = session('selected_project');
        $effectiveProjectId = data_get($selectedProject, 'id');
        return view('pays.edit_pay_email', compact('pay','chartAccounts', 'projects', 'contractors', 'contracts', 'contractsForFront', 'user', 'selectedProject', 'effectiveProjectId'));
    }
    public function updateEmail(Request $request, $id, $user_id)
    {
        $pay = Pay::findOrFail($id);
        $request->validate([
            'project_id' => 'nullable|string',
            'subproject' => 'nullable|string|max:255',
            'contractor_id' => 'nullable|string',
            'contract_id' => 'nullable|string',
            'chartAccount_id' => 'nullable|string',
            'payment_allocations' => 'nullable|array',
            'payment_allocations.*.chartAccount_id' => 'nullable|string|distinct',
            'payment_allocations.*.amount' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'attachment_link' => 'nullable|url',
        ]);

        $data = $this->applySelectedProject($request->all());
        if (empty($data['project_id'])) {
            $data['project_id'] = $pay->project_id;
        }
        $this->ensureActiveProject($data['project_id'] ?? null, $pay->project_id ?? null);

        if (empty($data['contractor_id'])) {
            $data['contractor_id'] = $pay->contractor_id;
        }

        if (empty($data['contract_id'])) {
            $data['contract_id'] = null;
        }

        if (empty($data['chartAccount_id'])) {
            $data['chartAccount_id'] = $pay->chartAccount_id;
        }

        $data = $this->preparePaymentBudgetData($data, (string) $pay->_id);

        $data['contract_id'] = $this->validateContractSelection(
            $data['contractor_id'] ?? null,
            $data['project_id'] ?? null,
            $data['chartAccount_id'] ?? null,
            $data['contract_id']
        );

        $data['amount'] = (float) $data['amount'];
        $user = User::findOrFail($user_id);
        $newHistory =
            [
                'user_id' => $user->_id,
                'user_name' => $user->name,
                'action' => 'Updated',
                'created_at' => now()
            ];


        $existingHistories = $pay->histories ?? [];
        $data['histories'] = array_merge($existingHistories, [$newHistory]);
        $previousContractId = $pay->contract_id ?? null;
        $pay->update($data);
        $this->syncPayContract($previousContractId, $pay->contract_id ?? null, (string) $pay->_id);
        $this->syncContractBudgetUsage($previousContractId);
        $this->syncContractBudgetUsage($pay->contract_id ?? null);
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Updated :name", ['name' => __('Payable')]),
        ]);

        return redirect()->route('pays.updatePay', ['id' => $pay->_id, 'user_id' => $user->_id]);

    }
    public function destroy(string $id)
    {
        $pay = Pay::findOrFail($id);
        $contractId = $pay->contract_id ?? null;
        $this->syncPayContract($pay->contract_id ?? null, null, (string) $pay->_id);
        $pay->delete();
        $this->syncContractBudgetUsage($contractId);

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Deleted :name", ['name' => __('Pay')])
        ]);

        return redirect()->route('pays.index');
    }

    private function syncPayContract(?string $previousContractId, ?string $newContractId, string $payId): void
    {
        if ($previousContractId && $previousContractId !== $newContractId) {
            $previousContract = Contract::find($previousContractId);

            if ($previousContract) {
                $payIds = array_values(array_filter(
                    $previousContract->pay_ids ?? [],
                    fn ($existingPayId) => (string) $existingPayId !== $payId
                ));

                $previousContract->update(['pay_ids' => $payIds]);
            }
        }

        if ($newContractId) {
            $newContract = Contract::find($newContractId);

            if ($newContract) {
                $payIds = array_map('strval', $newContract->pay_ids ?? []);

                if (!in_array($payId, $payIds, true)) {
                    $payIds[] = $payId;
                    $newContract->update(['pay_ids' => $payIds]);
                }
            }
        }
    }

    private function validateContractSelection(?string $contractorId, ?string $projectId, ?string $chartAccountId, ?string $contractId): ?string
    {
        if (!$contractId) {
            return null;
        }

        $contract = Contract::find($contractId);

        if (!$contract || (string) $contract->contractor_id !== (string) $contractorId) {
            throw ValidationException::withMessages([
                'contract_id' => __('The selected contract does not belong to the selected vendor.'),
            ]);
        }

        if ($projectId && (string) $contract->project_id !== (string) $projectId) {
            throw ValidationException::withMessages([
                'project_id' => __('The selected contract does not belong to the selected project.'),
            ]);
        }

        $allowedChartAccountIds = collect($contract->contract_budgets ?? [])
            ->pluck('chartAccount_id')
            ->map(fn ($id) => (string) $id)
            ->all();

        if ($chartAccountId && !in_array((string) $chartAccountId, $allowedChartAccountIds, true)) {
            throw ValidationException::withMessages([
                'chartAccount_id' => __('The selected budget code is not assigned to the selected contract.'),
            ]);
        }

        return (string) $contract->_id;
    }

    private function preparePaymentBudgetData(array $data, ?string $payId = null): array
    {
        $contractId = $data['contract_id'] ?? null;

        if (!$contractId) {
            if (empty($data['chartAccount_id'])) {
                throw ValidationException::withMessages([
                    'chartAccount_id' => __('The budget code field is required.'),
                ]);
            }

            $data['payment_allocations'] = [];
            return $data;
        }

        $allocations = collect($data['payment_allocations'] ?? [])
            ->filter(fn ($allocation) => filled($allocation['chartAccount_id'] ?? null) || filled($allocation['amount'] ?? null))
            ->map(fn ($allocation) => [
                'chartAccount_id' => (string) ($allocation['chartAccount_id'] ?? ''),
                'amount' => (float) ($allocation['amount'] ?? 0),
            ])
            ->filter(fn ($allocation) => filled($allocation['chartAccount_id']) && $allocation['amount'] > 0)
            ->values();

        if ($allocations->isEmpty()) {
            throw ValidationException::withMessages([
                'payment_allocations' => __('Select at least one budget code for this contract.'),
            ]);
        }

        $contract = Contract::find($contractId);

        if (!$contract) {
            throw ValidationException::withMessages([
                'contract_id' => __('The selected contract is invalid.'),
            ]);
        }

        $budgetByChartAccount = collect($contract->contract_budgets ?? [])
            ->mapWithKeys(fn ($budget) => [(string) ($budget['chartAccount_id'] ?? '') => (float) ($budget['budget'] ?? 0)]);
        $existingAllocationByChartAccount = $payId
            ? collect(Pay::find($payId)?->payment_allocations ?? [])
                ->mapWithKeys(fn ($allocation) => [
                    (string) ($allocation['chartAccount_id'] ?? '') => (float) ($allocation['amount'] ?? 0),
                ])
            : collect();

        foreach ($allocations as $allocation) {
            $chartAccountId = $allocation['chartAccount_id'];

            if (!$budgetByChartAccount->has($chartAccountId)) {
                throw ValidationException::withMessages([
                    'payment_allocations' => __('The selected budget code is not assigned to the selected contract.'),
                ]);
            }

            $remaining = $budgetByChartAccount[$chartAccountId] - $this->spentForContractBudget((string) $contract->_id, $chartAccountId, $payId);
            $availableForThisPay = $remaining + (float) ($existingAllocationByChartAccount[$chartAccountId] ?? 0);

            if ($allocation['amount'] > $availableForThisPay) {
                throw ValidationException::withMessages([
                    'payment_allocations' => __('The payment exceeds the available budget.'),
                ]);
            }
        }

        $data['payment_allocations'] = $allocations->all();
        $data['chartAccount_id'] = $allocations->first()['chartAccount_id'];
        $data['amount'] = $allocations->sum('amount');

        return $data;
    }

    private function applySelectedProject(array $data): array
    {
        $selectedProjectId = data_get(session('selected_project'), 'id');

        if (filled($selectedProjectId)) {
            $data['project_id'] = $selectedProjectId;
        }

        return $data;
    }

    private function ensureActiveProject(?string $projectId, ?string $allowedInactiveProjectId = null): void
    {
        if (blank($projectId) || (filled($allowedInactiveProjectId) && (string) $projectId === (string) $allowedInactiveProjectId)) {
            return;
        }

        if (!Project::active()->find($projectId)) {
            throw ValidationException::withMessages([
                'project_id' => __('The selected project is invalid.'),
            ]);
        }
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

    private function contractsForFront(?string $excludePayId = null)
    {
        $chartAccountNames = ChartAccount::all()
            ->mapWithKeys(fn ($chartAccount) => [(string) $chartAccount->_id => $chartAccount->name]);

        return Contract::all()
            ->map(function ($contract) use ($chartAccountNames, $excludePayId) {
            $budgets = collect($contract->contract_budgets ?? [])->map(function ($budget) use ($contract, $chartAccountNames, $excludePayId) {
                $chartAccountId = (string) ($budget['chartAccount_id'] ?? '');
                $budgetAmount = (float) ($budget['budget'] ?? 0);
                $spent = $this->spentForContractBudget((string) $contract->_id, $chartAccountId, $excludePayId);
                $remaining = max($budgetAmount - $spent, 0);

                return [
                    'chartAccount_id' => $chartAccountId,
                    'name' => $chartAccountNames[$chartAccountId] ?? '',
                    'budget' => $budgetAmount,
                    'spent' => $spent,
                    'remaining' => $remaining,
                ];
            })->values();

            return [
                'id' => (string) $contract->_id,
                'name' => $contract->name,
                'contractor_id' => (string) $contract->contractor_id,
                'project_id' => (string) ($contract->project_id ?? ''),
                'subproject' => (string) ($contract->subproject ?? ''),
                'budgets' => $budgets,
            ];
        })->values();
    }

    private function spentForContractBudget(string $contractId, string $chartAccountId, ?string $excludePayId = null): float
    {
        $query = Pay::where('contract_id', $contractId)->where('status', '!=', 1);

        if ($excludePayId) {
            $query->where('_id', '!=', $excludePayId);
        }

        return $query->get()->sum(function ($pay) use ($chartAccountId) {
            $allocations = collect($pay->payment_allocations ?? []);

            if ($allocations->isNotEmpty()) {
                return $allocations
                    ->where('chartAccount_id', $chartAccountId)
                    ->sum(fn ($allocation) => (float) ($allocation['amount'] ?? 0));
            }

            return (string) ($pay->chartAccount_id ?? '') === $chartAccountId ? (float) ($pay->amount ?? 0) : 0;
        });
    }

    private function syncContractBudgetUsage(?string $contractId): void
    {
        if (!$contractId) {
            return;
        }

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




}
