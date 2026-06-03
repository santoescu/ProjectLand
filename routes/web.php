<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Models\Project;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChartAccountController;
use App\Http\Controllers\PayController;
use App\Livewire\Settings\Language;


Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', function () {
    session()->forget('selected_project');

    $projects = Project::query()->active()->orderBy('name')->get();

    return view('dashboard', compact('projects'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('dashboard/select-project', function () {
    request()->validate([
        'project_id' => 'required',
    ]);

    $project = Project::findOrFail(request('project_id'));

    session([
        'selected_project' => [
            'id' => (string) $project->_id,
            'name' => $project->name,
            'status' => $project->status,
        ],
    ]);

    return redirect()->route('pays.index');
})->middleware(['auth', 'verified'])->name('dashboard.select-project');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/language', Language::class)->name('settings.language');

    Route::middleware(['role:accounting_assistant,director,admin'])->group(function () {
        Route::resource('contractors', ContractorController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('chartAccounts', ChartAccountController::class)->except(['show']);
        Route::get('chartAccounts/tree', [ChartAccountController::class, 'tree'])
            ->name('chartAccounts.tree');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware(['role:director,accounting_assistant,project_manager,admin'])->group(function () {
        Route::get('contracts/{id}/payment-detail-table', [ContractController::class, 'paymentDetailTable'])
            ->name('contracts.paymentDetailTable');
        Route::resource('pays', PayController::class);
        Route::resource('contracts', ContractController::class);
    });



});
Route::get('/pays/{id}/status/{status}/{user_id}', [PayController::class, 'updateStatus'])->name('pays.updateStatus');
Route::put('/pays/{id}/{user_id}/update', [PayController::class, 'updateEmail'])->name('pays.updateEmail');
Route::get('/pays/{id}/{user_id}', [PayController::class, 'updatePay'])->name('pays.updatePay');





require __DIR__.'/auth.php';
