<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use App\Models\Contractor;
use App\Models\Pay;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class PayController extends Controller
{
    public function index(Request $request)
    {
        $query = Pay::with('project', 'contractor', 'chartAccount');

        if (!is_null($request->status) && $request->status !== '') {
            $query->where('status', (int) $request->status);
        }


        $pays = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pays.index', compact('pays'));
    }
    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {

        $chartAccounts = ChartAccount::all();
        $projects = Project::all();
        $contractors = Contractor::all();
        return view('pays.create', compact('chartAccounts', 'projects', 'contractors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|string',
            'subproject' => 'nullable|string|max:255',
            'contractor_id' => 'required|string',
            'chartAccount_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['amount'] = (float) $data['amount'];
        $users = User::where('role', 'director')->get();
        $data['user_id'] = $users->pluck('_id')->toArray();;
        $data['status'] = 0;
        $data['histories'] = [
            [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'created',
                'created_at' => now()
            ]
        ];

        $pay = Pay::create($data);

        try {
            foreach ($users as $user) {
                $emailData = [
                    'pay' => $pay,
                    'user' => $user, // importante: pasamos el usuario al email
                ];

                Mail::send('emails.pays', $emailData, function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Nuevo pago creado');
                });
            }
        } catch (Exception $e) {

        }



        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Created :name", ['name' => __('Pay')]),
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
            'chartAccount_id' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['project_id'])) {
            $data['project_id'] = $pay->project_id;
        }

        if (empty($data['contractor_id'])) {
            $data['contractor_id'] = $pay->contractor_id;
        }

        if (empty($data['chartAccount_id'])) {
            $data['chartAccount_id'] = $pay->chartAccount_id;
        }
        $data['amount'] = (float) $data['amount'];
        $newHistory =
            [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'action' => 'updated',
                'created_at' => now()
            ];


        $existingHistories = $pay->histories ?? [];
        $data['histories'] = array_merge($existingHistories, [$newHistory]);
        $pay->update($data);
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Updated :name", ['name' => __('Pay')]),
        ]);
        return redirect()->route('pays.index');
    }
    public function edit($id)
    {

        $pay= Pay::findOrFail($id);

        $chartAccounts = ChartAccount::all();
        $projects = Project::all();
        $contractors = Contractor::all();
        return view('pays.edit', compact('pay','chartAccounts', 'projects', 'contractors'));
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

                    Mail::send('emails.pays', $emailData, function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('Nuevo pago creado');
                    });
                }
            } catch (Exception $e) {

            }
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Updated :name", ['name' => __('Pay')]),
        ]);
        return back();
    }

    public function updatePay($id, $user_id)
    {

        $pay= Pay::findOrFail($id);
        $user = User::findOrFail($user_id);

        $chartAccounts = ChartAccount::all();
        $projects = Project::all();
        $contractors = Contractor::all();
        return view('pays.edit_pay_email', compact('pay','chartAccounts', 'projects', 'contractors', 'user'));
    }
    public function updateEmail(Request $request, $id, $user_id)
    {
        $pay = Pay::findOrFail($id);
        $request->validate([
            'project_id' => 'nullable|string',
            'subproject' => 'nullable|string|max:255',
            'contractor_id' => 'nullable|string',
            'chartAccount_id' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['project_id'])) {
            $data['project_id'] = $pay->project_id;
        }

        if (empty($data['contractor_id'])) {
            $data['contractor_id'] = $pay->contractor_id;
        }

        if (empty($data['chartAccount_id'])) {
            $data['chartAccount_id'] = $pay->chartAccount_id;
        }
        $data['amount'] = (float) $data['amount'];
        $user = User::findOrFail($user_id);
        $newHistory =
            [
                'user_id' => $user->_id,
                'user_name' => $user->name,
                'action' => 'updated',
                'created_at' => now()
            ];


        $existingHistories = $pay->histories ?? [];
        $data['histories'] = array_merge($existingHistories, [$newHistory]);
        $pay->update($data);
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Updated :name", ['name' => __('Pay')]),
        ]);

        return redirect()->route('pays.updatePay', ['id' => $pay->_id, 'user_id' => $user->_id]);

    }




}
