<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\UserPrivilegeController;
use App\Http\Controllers\Settings\LedgerControlController;
use App\Http\Controllers\Settings\LedgerCategoryController;
use App\Http\Controllers\Settings\LedgerAccountController;
use App\Http\Controllers\Settings\HandlingChargeController;

use Illuminate\Support\Facades\Route;


// Guest Routes — accessible only when NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware("throttle:login")->name('login.submit');
    //forgot password request
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware("throttle:forgot-password")->name('password.request');
});

Route::get('/', function () {
    return redirect()->route('login');
});


//Authenticated Routes — accessible only when logged in
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //change password routes
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.update');


    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Settings
    Route::prefix('settings')->name('settings.')->group(function () {

        // User Privilege
        Route::middleware('permission:UserPrivilege')->group(function () {
            Route::get('/user-privilege', [UserPrivilegeController::class, 'index'])->name('user-privilege.index');
            Route::get('/user-privilege/{userId}', [UserPrivilegeController::class, 'show'])->name('user-privilege.show');
            Route::post('/user-privilege/initialise', [UserPrivilegeController::class, 'initialise'])->name('user-privilege.initialise');
            Route::post('/user-privilege/toggle', [UserPrivilegeController::class, 'toggle'])->name('user-privilege.toggle');
            Route::post('/user-privilege/reset-password', [UserPrivilegeController::class, 'resetPassword'])->name('user-privilege.reset-password');
        });

        // Ledger Control — requires BasicConfig permission
        Route::middleware('permission:BasicConfig')->group(function () {
            //Ledger Control routes
            Route::get('/ledger-control', [LedgerControlController::class, 'index'])->name('ledger-control.index');
            Route::post('/ledger-control', [LedgerControlController::class, 'store'])->name('ledger-control.store');
            Route::put('/ledger-control/{id}', [LedgerControlController::class, 'update'])->name('ledger-control.update');
            Route::patch('/ledger-control/{id}/deactivate', [LedgerControlController::class, 'deactivate'])->name('ledger-control.deactivate');
            Route::patch('/ledger-control/{id}/restore', [LedgerControlController::class, 'restore'])->name('ledger-control.restore');

            //Ledger Category routes
            Route::get('/ledger-category', [LedgerCategoryController::class, 'index'])->name('ledger-category.index');
            Route::post('/ledger-category/category', [LedgerCategoryController::class, 'storeCategory'])->name('ledger-category.store-category');
            Route::post('/ledger-category', [LedgerCategoryController::class, 'storeSubCategory'])->name('ledger-category.store');
            Route::put('/ledger-category/{id}', [LedgerCategoryController::class, 'update'])->name('ledger-category.update');
            Route::patch('/ledger-category/{id}/deactivate', [LedgerCategoryController::class, 'deactivate'])->name('ledger-category.deactivate');
            Route::patch('/ledger-category/{id}/restore', [LedgerCategoryController::class, 'restore'])->name('ledger-category.restore');

            //Ledger account routes
            Route::get('/ledger-account', [LedgerAccountController::class, 'index'])->name('ledger-account.index');
            Route::post('/ledger-account', [LedgerAccountController::class, 'store'])->name('ledger-account.store');
            Route::put('/ledger-account/{id}', [LedgerAccountController::class, 'update'])->name('ledger-account.update');
            Route::patch('/ledger-account/{id}/toggle-visible', [LedgerAccountController::class, 'toggleVisible'])->name('ledger-account.toggle-visible');
            Route::patch('/ledger-account/{id}/deactivate', [LedgerAccountController::class, 'deactivate'])->name('ledger-account.deactivate');
            Route::patch('/ledger-account/{id}/restore', [LedgerAccountController::class, 'restore'])->name('ledger-account.restore');
            Route::get('/ledger-account/categories-by-type', [LedgerAccountController::class, 'categoriesByType'])->name('ledger-account.categories-by-type');

            // Handling Charge
            Route::get('/handling-charge', [HandlingChargeController::class, 'index'])->name('handling-charge.index');
            Route::post('/handling-charge', [HandlingChargeController::class, 'store'])->name('handling-charge.store');
            Route::put('/handling-charge/{id}', [HandlingChargeController::class, 'update'])->name('handling-charge.update');
            Route::delete('/handling-charge/{id}', [HandlingChargeController::class, 'destroy'])->name('handling-charge.destroy');
        });
    });
});
