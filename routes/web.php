<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Settings\UserPrivilegeController;
use Illuminate\Support\Facades\Route;


// Guest Routes — accessible only when NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::get('/', function () {
    return redirect()->route('login');
});


//Authenticated Routes — accessible only when logged in
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    //Settings
    Route::prefix('settings')->name('settings.')->group(function () {

        // User Privilege
        Route::middleware('permission:UserPrivilege')->group(function () {
            Route::get('/user-privilege', [UserPrivilegeController::class, 'index'])->name('user-privilege.index');
            Route::get('/user-privilege/{userId}', [UserPrivilegeController::class, 'show'])->name('user-privilege.show');
            Route::post('/user-privilege/initialise', [UserPrivilegeController::class, 'initialise'])->name('user-privilege.initialise');
            Route::post('/user-privilege/toggle', [UserPrivilegeController::class, 'toggle'])->name('user-privilege.toggle');
        });
    });
});
