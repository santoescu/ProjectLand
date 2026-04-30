<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Livewire\Settings\Language;


Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', function () {
    return redirect()->route('inventories.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/language', Language::class)->name('settings.language');
    Route::resource('inventories', InventoryController::class)
        ->only(['index'])
        ->middleware('role:admin,manager,viewer');

    Route::resource('inventories', InventoryController::class)
        ->except(['index', 'show'])
        ->middleware('role:admin,manager');

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
