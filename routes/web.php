<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\UserPrivilegeController;
use App\Http\Controllers\Settings\LedgerControlController;
use App\Http\Controllers\Settings\LedgerCategoryController;
use App\Http\Controllers\Settings\LedgerAccountController;
use App\Http\Controllers\Settings\HandlingChargeController;
use App\Http\Controllers\Settings\DisbursementAccountController;
use App\Http\Controllers\Settings\ActiveAccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsigneeController;
use App\Http\Controllers\MasterData\ShipperController;
use App\Http\Controllers\MasterData\CarrierController;
use App\Http\Controllers\MasterData\PortController;
use App\Http\Controllers\MasterData\CommodityController;
use App\Http\Controllers\ConsignmentController;


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


    //Consignee management
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/consignees', [ConsigneeController::class, 'index'])->name('consignees.index');
        Route::post('/consignees', [ConsigneeController::class, 'store'])->name('consignees.store');
        Route::put('/consignees/{id}', [ConsigneeController::class, 'update'])->name('consignees.update');
        Route::patch('/consignees/{id}/deactivate', [ConsigneeController::class, 'deactivate'])->name('consignees.deactivate');
        Route::patch('/consignees/{id}/restore', [ConsigneeController::class, 'restore'])->name('consignees.restore');
        Route::get('/consignees/search', [ConsigneeController::class, 'search'])->name('consignees.search');
        //AJAX table search
        Route::get('/consignees/table', [ConsigneeController::class, 'table'])->name('consignees.table');

        // Shippers
        Route::get('/shippers', [ShipperController::class, 'index'])->name('shippers.index');
        Route::post('/shippers', [ShipperController::class, 'store'])->name('shippers.store');
        Route::put('/shippers/{id}', [ShipperController::class, 'update'])->name('shippers.update');
        Route::patch('/shippers/{id}/deactivate', [ShipperController::class, 'deactivate'])->name('shippers.deactivate');
        Route::patch('/shippers/{id}/restore', [ShipperController::class, 'restore'])->name('shippers.restore');

        // Carriers
        Route::get('/carriers', [CarrierController::class, 'index'])->name('carriers.index');
        Route::post('/carriers', [CarrierController::class, 'store'])->name('carriers.store');
        Route::put('/carriers/{id}', [CarrierController::class, 'update'])->name('carriers.update');
        Route::patch('/carriers/{id}/deactivate', [CarrierController::class, 'deactivate'])->name('carriers.deactivate');
        Route::patch('/carriers/{id}/restore', [CarrierController::class, 'restore'])->name('carriers.restore');

        // Ports (POL + POD combined)
        Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
        Route::post('/ports/pol', [PortController::class, 'storePol'])->name('ports.pol.store');
        Route::put('/ports/pol/{id}', [PortController::class, 'updatePol'])->name('ports.pol.update');
        Route::delete('/ports/pol/{id}', [PortController::class, 'destroyPol'])->name('ports.pol.destroy');
        Route::post('/ports/pod', [PortController::class, 'storePod'])->name('ports.pod.store');
        Route::put('/ports/pod/{id}', [PortController::class, 'updatePod'])->name('ports.pod.update');
        Route::delete('/ports/pod/{id}', [PortController::class, 'destroyPod'])->name('ports.pod.destroy');

        // Commodities
        Route::get('/commodities', [CommodityController::class, 'index'])->name('commodities.index');
        Route::post('/commodities/category', [CommodityController::class, 'storeCategory'])->name('commodities.category.store');
        Route::post('/commodities/type', [CommodityController::class, 'storeType'])->name('commodities.type.store');
        Route::delete('/commodities/type/{id}', [CommodityController::class, 'destroyType'])->name('commodities.type.destroy');
    });



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

            Route::get('/disbursement-account', [DisbursementAccountController::class, 'index'])->name('disbursement-account.index');
            Route::post('/disbursement-account', [DisbursementAccountController::class, 'store'])->name('disbursement-account.store');
            Route::delete('/disbursement-account/{id}', [DisbursementAccountController::class, 'destroy'])->name('disbursement-account.destroy');

            Route::get('/active-accounts', [ActiveAccountController::class, 'index'])->name('active-accounts.index');
            Route::put('/active-accounts/{key}', [ActiveAccountController::class, 'update'])->name('active-accounts.update');
        });
    });

    // Consignment Register — requires ConsignmentRegister permission
    Route::middleware('permission:ConsignmentRegister')->prefix('consignments')->name('consignments.')->group(function () {
        Route::get('/new', [ConsignmentController::class, 'create'])->name('create');
        Route::post('/containers/add', [ConsignmentController::class, 'addContainer'])->name('containers.add');
        Route::delete('/containers/remove', [ConsignmentController::class, 'removeContainer'])->name('containers.remove');
        Route::delete('/containers/clear', [ConsignmentController::class, 'clearContainers'])->name('containers.clear');
        Route::post('/ocr', [ConsignmentController::class, 'extractFromBL'])->name('ocr');
        Route::post('/', [ConsignmentController::class, 'store'])->name('store');
    });
});
