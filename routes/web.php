<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChartAccountController;
use App\Http\Controllers\PayController;
use App\Livewire\Settings\Language;


Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/language', Language::class)->name('settings.language');

    Route::middleware(['role:accounting_assistant,director'])->group(function () {
        Route::resource('contractors', ContractorController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('chartAccounts', ChartAccountController::class)->except(['show']);
        Route::get('chartAccounts/tree', [ChartAccountController::class, 'tree'])
            ->name('chartAccounts.tree');
    });

    Route::middleware(['role:,director'])->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware(['role:,director,accounting_assistant,project_manager'])->group(function () {
        Route::resource('pays', PayController::class);
    });

    Route::get('/pays/{id}/status/{status}/{user_id}', [PayController::class, 'updateStatus'])->name('pays.updateStatus');

    Route::get('/pays/{id}/{user_id}', [PayController::class, 'updatePay'])->name('pays.updatePay');
    Route::put('/pays/{id}/{user_id}/update', [PayController::class, 'updateEmail'])->name('pays.updateEmail');

});






require __DIR__.'/auth.php';
