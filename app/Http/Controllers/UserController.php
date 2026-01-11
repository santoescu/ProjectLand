<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{

    public function index(Request $request)
    {

        $query = User::query();

        $users = $query->paginate(10);

        return view('users.index', compact('users'));
    }
    public function create()
    {
        return view('users.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'role' => 'required|string|in:accounting_assistant,project_manager,director,admin'
        ]);

        $temporaryPassword = Str::password(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($temporaryPassword),
        ]);

        $emailData = [
            'user' => $user,
            'temporaryPassword' => $temporaryPassword, // 👈 la enviamos a la vista
        ];

        Mail::send('emails.newUser', $emailData, function ($message) use ($user) {
            $message->to($user->email)
                ->subject($user->name." Project Land - New User");
        });
        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Created :name", ['name' => __('User')])
        ]);
        return redirect()->route('users.index');
    }
    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($id),
            ],
            'role' => 'required|string|in:accounting_assistant,project_manager,director,admin'
        ]);

        $user = User::findOrFail($id);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->update($request->all());
        return redirect()->route('users.index');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => __("Deleted :name", ['name' => __('Vendor')])
        ]);

        return redirect()->route('users.index');
    }
}
