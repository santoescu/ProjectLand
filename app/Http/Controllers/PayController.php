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


        $pays = $query->orderBy('created_at', 'desc')->paginate(10);
        $projects = Project::all();

        return view('pays.index', compact('projects', 'pays', 'selectedProject', 'effectiveProjectId'));
    }
    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {

        $chartAccounts = ChartAccount::all();
        $contracts = Contract::all();
        $projects = Project::all();
        $contractors = Contractor::all();
        return view('pays.create', compact('chartAccounts', 'projects', 'contractors', 'contracts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|string',
            'subproject' => 'nullable|string|max:255',
            'contractor_id' => 'required|string',
            'contract_id' => 'nullable|string',
            'chartAccount_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'attachment_link' => 'nullable|url',
        ]);

        $data = $request->all();
        $data['contract_id'] = $this->validateContractSelection($data['contractor_id'] ?? null, $data['contract_id'] ?? null);
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
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'attachment_link' => 'nullable|url',
        ]);

        $data = $request->all();
        if (empty($data['project_id'])) {
            $data['project_id'] = $pay->project_id;
        }

        if (empty($data['contractor_id'])) {
            $data['contractor_id'] = $pay->contractor_id;
        }

        if (empty($data['contract_id'])) {
            $data['contract_id'] = null;
        }

        $data['contract_id'] = $this->validateContractSelection($data['contractor_id'] ?? null, $data['contract_id']);

        if (empty($data['chartAccount_id'])) {
            $data['chartAccount_id'] = $pay->chartAccount_id;
        }
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
        $projects = Project::all();
        $contractors = Contractor::all();
        return view('pays.edit', compact('pay','chartAccounts', 'projects', 'contractors', 'contracts'));
    }

    public function updateStatus($id, $status,$user_id)
    {
        $pay = Pay::findOrFail($id);

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
        return back();
    }

    public function updatePay($id, $user_id)
    {

        $pay= Pay::findOrFail($id);
        $user = User::findOrFail($user_id);

        $chartAccounts = ChartAccount::all();
        $contracts = Contract::all();
        $projects = Project::all();
        $contractors = Contractor::all();
        return view('pays.edit_pay_email', compact('pay','chartAccounts', 'projects', 'contractors', 'contracts', 'user'));
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
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'attachment_link' => 'nullable|url',
        ]);

        $data = $request->all();
        if (empty($data['project_id'])) {
            $data['project_id'] = $pay->project_id;
        }

        if (empty($data['contractor_id'])) {
            $data['contractor_id'] = $pay->contractor_id;
        }

        if (empty($data['contract_id'])) {
            $data['contract_id'] = null;
        }

        $data['contract_id'] = $this->validateContractSelection($data['contractor_id'] ?? null, $data['contract_id']);

        if (empty($data['chartAccount_id'])) {
            $data['chartAccount_id'] = $pay->chartAccount_id;
        }
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
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Updated :name", ['name' => __('Payable')]),
        ]);

        return redirect()->route('pays.updatePay', ['id' => $pay->_id, 'user_id' => $user->_id]);

    }
    public function destroy(string $id)
    {
        $pay = Pay::findOrFail($id);
        $this->syncPayContract($pay->contract_id ?? null, null, (string) $pay->_id);
        $pay->delete();

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

    private function validateContractSelection(?string $contractorId, ?string $contractId): ?string
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

        return (string) $contract->_id;
    }




}
