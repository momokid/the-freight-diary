<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CmdtsController;
use App\Http\Controllers\ConsigneeController;
use App\Http\Controllers\ConsignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisbursementAnalysisController;
use App\Http\Controllers\DisbursementApprovalController;
use App\Http\Controllers\GateOutExpenseController;
use App\Http\Controllers\HblInvoiceController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\MasterData\CarrierController;
use App\Http\Controllers\MasterData\CommodityController;
use App\Http\Controllers\MasterData\PortController;
use App\Http\Controllers\MasterData\ShipperController;
use App\Http\Controllers\NonManifestInvoiceController;
use App\Http\Controllers\OtherExpenditureController;
use App\Http\Controllers\OtherInvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Settings\ActiveAccountController;
use App\Http\Controllers\Settings\DisbursementAccountController;
use App\Http\Controllers\Settings\HandlingChargeController;
use App\Http\Controllers\Settings\LedgerAccountController;
use App\Http\Controllers\Settings\LedgerCategoryController;
use App\Http\Controllers\Settings\LedgerControlController;
use App\Http\Controllers\Settings\UserPrivilegeController;
use App\Http\Controllers\WaybillController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Guest Routes — accessible only when NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.submit');
    // forgot password request
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:forgot-password')->name('password.request');
});

Route::get('/', function () {
    return redirect()->route('login');
});

// Authenticated Routes — accessible only when logged in
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // change password routes
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.update');

    // Fresh receipt number — called after every successful save
    Route::get('/receipt/generate', function (\Illuminate\Http\Request $request) {
        $date = $request->date ?? now()->toDateString();
        $receipt = \App\Services\ReceiptService::generate($date);

        return response()->json($receipt);
    })->name('receipt.generate');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Consignee management
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/consignees', [ConsigneeController::class, 'index'])->name('consignees.index');
        Route::post('/consignees', [ConsigneeController::class, 'store'])->name('consignees.store');
        Route::put('/consignees/{id}', [ConsigneeController::class, 'update'])->name('consignees.update');
        Route::patch('/consignees/{id}/deactivate', [ConsigneeController::class, 'deactivate'])->name('consignees.deactivate');
        Route::patch('/consignees/{id}/restore', [ConsigneeController::class, 'restore'])->name('consignees.restore');
        Route::get('/consignees/search', [ConsigneeController::class, 'search'])->name('consignees.search');
        // AJAX table search
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

    // Settings
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
            // Ledger Control routes
            Route::get('/ledger-control', [LedgerControlController::class, 'index'])->name('ledger-control.index');
            Route::post('/ledger-control', [LedgerControlController::class, 'store'])->name('ledger-control.store');
            Route::put('/ledger-control/{id}', [LedgerControlController::class, 'update'])->name('ledger-control.update');
            Route::patch('/ledger-control/{id}/deactivate', [LedgerControlController::class, 'deactivate'])->name('ledger-control.deactivate');
            Route::patch('/ledger-control/{id}/restore', [LedgerControlController::class, 'restore'])->name('ledger-control.restore');

            // Ledger Category routes
            Route::get('/ledger-category', [LedgerCategoryController::class, 'index'])->name('ledger-category.index');
            Route::post('/ledger-category/category', [LedgerCategoryController::class, 'storeCategory'])->name('ledger-category.store-category');
            Route::post('/ledger-category', [LedgerCategoryController::class, 'storeSubCategory'])->name('ledger-category.store');
            Route::put('/ledger-category/{id}', [LedgerCategoryController::class, 'update'])->name('ledger-category.update');
            Route::patch('/ledger-category/{id}/deactivate', [LedgerCategoryController::class, 'deactivate'])->name('ledger-category.deactivate');
            Route::patch('/ledger-category/{id}/restore', [LedgerCategoryController::class, 'restore'])->name('ledger-category.restore');

            // Ledger account routes
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

    // Consignment and Manifest routes — requires ConsignmentRegister permission
    Route::middleware('permission:ConsignmentRegister')->group(function () {

        // Consignment routes
        Route::prefix('consignments')->name('consignments.')->group(function () {
            Route::get('/new', [ConsignmentController::class, 'create'])->name('create');
            Route::post('/containers/add', [ConsignmentController::class, 'addContainer'])->name('containers.add');
            Route::delete('/containers/remove', [ConsignmentController::class, 'removeContainer'])->name('containers.remove');
            Route::delete('/containers/clear', [ConsignmentController::class, 'clearContainers'])->name('containers.clear');
            Route::post('/ocr', [ConsignmentController::class, 'extractFromBL'])->name('ocr');
            Route::post('/', [ConsignmentController::class, 'store'])->name('store');
        });

        // Manifest routes
        Route::prefix('manifest')->name('manifest.')->group(function () {
            Route::get('/', [ManifestController::class, 'index'])->name('index');
            Route::get('/search', [ManifestController::class, 'search'])->name('search');
            Route::get('/generate-hbl', [ManifestController::class, 'generateHouseBL'])->name('generate-hbl');
            Route::post('/entries/add', [ManifestController::class, 'addEntry'])->name('entries.add');
            Route::delete('/entries/remove', [ManifestController::class, 'removeEntry'])->name('entries.remove');
            Route::delete('/entries/clear', [ManifestController::class, 'clearEntries'])->name('entries.clear');
            Route::post('/store', [ManifestController::class, 'store'])->name('store');
            Route::get('/consignee-search', [ManifestController::class, 'searchConsignee'])->name('consignee-search')->middleware('throttle:60,1');
            Route::get('/search-bl', [ManifestController::class, 'searchBL'])
                ->name('search-bl')
                ->middleware('throttle:60,1');
            Route::get('/manifest-breakdown/{bl}', [ManifestController::class, 'report'])->name('manifest.breakdown');
        });

        // // Consignment Cmdts routes
        Route::prefix('cmdts')->name('cmdts.')->group(function () {
            Route::get('/', [CmdtsController::class, 'index'])->name('index');
            Route::post('/containers/add', [CmdtsController::class, 'addContainer'])->name('containers.add');
            Route::delete('/containers/remove', [CmdtsController::class, 'removeContainer'])->name('containers.remove');
            Route::delete('/containers/clear', [CmdtsController::class, 'clearContainers'])->name('containers.clear');
            Route::post('/', [CmdtsController::class, 'store'])->name('store');
            Route::get('/consignee-search', [CmdtsController::class, 'searchConsignee'])->name('consignee-search')->middleware('throttle:60,1');
            Route::get('/types-by-category', [CmdtsController::class, 'typesByCategory'])->name('types-by-category');
            Route::post('/release/store', function (\Illuminate\Http\Request $request) {
                $request->validate(['name' => ['required', 'string', 'max:100']]);
                $id = DB::table('container_release')->insertGetId(['ReleaseType' => $request->name]);

                return response()->json(['success' => true, 'id' => $id, 'name' => $request->name]);
            })->name('release.store');
        });
    });

    // Generate Invoice
    Route::middleware('permission:GenerateInvoice')->prefix('invoice')->name('invoice.')->group(function () {

        // House BL Invoice
        Route::prefix('house-bl')->name('hbl.')->group(function () {
            Route::get('/', [HblInvoiceController::class, 'index'])->name('index');
            Route::get('/search', [HblInvoiceController::class, 'search'])->name('search')->middleware('throttle:60,1');
            Route::post('/charges/add', [HblInvoiceController::class, 'addCharge'])->name('charges.add');
            Route::delete('/charges/remove', [HblInvoiceController::class, 'removeCharge'])->name('charges.remove');
            Route::delete('/charges/clear', [HblInvoiceController::class, 'clearCharges'])->name('charges.clear');
            Route::post('/store', [HblInvoiceController::class, 'store'])->name('store');
            Route::get('/report/{hbl}', [HblInvoiceController::class, 'report'])->name('report');
        });

        // Customer Waybill
        Route::prefix('waybill')->name('waybill.')->group(function () {
            Route::get('/', [WaybillController::class, 'index'])->name('index');
            Route::get('/search', [WaybillController::class, 'search'])->name('search')->middleware('throttle:60,1');
            Route::post('/', [WaybillController::class, 'store'])->name('store');
            Route::get('/report/{id}', [WaybillController::class, 'report'])->name('report');
        });

        // Other Serv. Invoice
        Route::prefix('other-invoice')->name('other-invoice.')->group(function () {
            Route::get('/', [OtherInvoiceController::class, 'index'])->name('index');
            Route::get('/search-client', [OtherInvoiceController::class, 'searchClient'])->name('search-client')->middleware('throttle:60,1');
            Route::post('/charges/add', [OtherInvoiceController::class, 'addCharge'])->name('charges.add');
            Route::delete('/charges/remove', [OtherInvoiceController::class, 'removeCharge'])->name('charges.remove');
            Route::delete('/charges/clear', [OtherInvoiceController::class, 'clearCharges'])->name('charges.clear');
            Route::post('/store', [OtherInvoiceController::class, 'store'])->name('store');
            Route::get('/report/{receiptNo}', [OtherInvoiceController::class, 'report'])->name('report');
        });

        // Non-Manifest Invoice
        Route::prefix('non-manifest')->name('non-manifest.')->group(function () {
            Route::get('/', [NonManifestInvoiceController::class, 'index'])->name('index');
            Route::get('/search-client', [NonManifestInvoiceController::class, 'searchClient'])->name('search-client')->middleware('throttle:60,1');
            Route::get('/get-bls', [NonManifestInvoiceController::class, 'getBLs'])->name('get-bls')->middleware('throttle:60,1');
            Route::post('/charges/add', [NonManifestInvoiceController::class, 'addCharge'])->name('charges.add');
            Route::delete('/charges/remove', [NonManifestInvoiceController::class, 'removeCharge'])->name('charges.remove');
            Route::delete('/charges/clear', [NonManifestInvoiceController::class, 'clearCharges'])->name('charges.clear');
            Route::post('/store', [NonManifestInvoiceController::class, 'store'])->name('store');
            Route::get('/report/{receiptNo}', [NonManifestInvoiceController::class, 'report'])->name('report');
        });

    });

    Route::middleware('auth')->prefix('payment')->name('payment.')->group(function () {

        // Process Declaration form + save — requires PaymentTransaction permission
        Route::middleware('permission:PaymentTransaction')->prefix('declaration')->name('declaration.')->group(function () {
            Route::get('/', [PaymentController::class, 'declaration'])->name('index');
            Route::post('/store', [PaymentController::class, 'storeDeclaration'])->name('store');
            Route::get('/search-bl', [PaymentController::class, 'searchBL'])->name('search-bl')->middleware('throttle:60,1');
        });

        // Declaration report — auth only, outside permission middleware
        Route::get('/declaration/report/{receiptNo}', [PaymentController::class, 'declarationReport'])->name('declaration.report');

        // Receive Handling Charge — requires PaymentTransaction permission
        Route::middleware('permission:PaymentTransaction')->prefix('handl-charge')->name('handl-charge.')->group(function () {
            Route::get('/', [PaymentController::class, 'handlCharge'])->name('index');
            Route::post('/store', [PaymentController::class, 'storeHandlCharge'])->name('store');
            Route::get('/search-hbl', [PaymentController::class, 'searchHBLForPayment'])->name('search-hbl')->middleware('throttle:60,1');
            Route::get('/get-balance', [PaymentController::class, 'getHBLBalance'])->name('get-balance')->middleware('throttle:60,1');
        });

        // Handling Charge receipt — auth only, outside permission middleware
        Route::get('/handl-charge/report/{receiptNo}', [PaymentController::class, 'handlChargeReport'])->name('handl-charge.report');

        // Receive Service Charge — requires PaymentTransaction permission
        Route::middleware('permission:PaymentTransaction')->prefix('serv-charge')->name('serv-charge.')->group(function () {
            Route::get('/', [PaymentController::class, 'servCharge'])->name('index');
            Route::post('/store', [PaymentController::class, 'storeServCharge'])->name('store');
            Route::get('/search-dcl', [PaymentController::class, 'searchDclForServCharge'])->name('search-dcl')->middleware('throttle:60,1');
        });

        // Service Charge receipt — auth only, outside permission middleware
        Route::get('/serv-charge/report/{receiptNo}', [PaymentController::class, 'servChargeReport'])->name('serv-charge.report');

        // Handling Charge Expense — requires PaymentTransaction permission
        Route::middleware('permission:PaymentTransaction')->prefix('handling-charge-expense')->name('handling-charge-expense.')->group(function () {
            Route::get('/', [PaymentController::class, 'handlingChargeExpense'])->name('index');
            Route::post('/store', [PaymentController::class, 'storeHandlingChargeExpense'])->name('store');
            Route::get('/search-main-bl', [PaymentController::class, 'searchMainBLForExpense'])->name('search-main-bl')->middleware('throttle:60,1');
            Route::get('/get-consignment', [PaymentController::class, 'getConsignmentForExpense'])->name('get-consignment')->middleware('throttle:60,1');
        });

        // Handling Charge Expense receipt — auth only, outside permission middleware
        Route::get('/handling-charge-expense/report/{receiptNo}', [PaymentController::class, 'handlingChargeExpenseReport'])->name('handling-charge-expense.report');

    });

    // Accounting Transaction
    Route::middleware('auth')->prefix('accounting')->name('accounting.')->group(function () {

        Route::middleware('permission:GLTransaction')->group(function () {
            Route::get('/transaction', [AccountingController::class, 'transaction'])->name('transaction.index');
            Route::post('/transaction/store', [AccountingController::class, 'storeTransaction'])->name('transaction.store');
        });

    });

    // Disbursement Analysis
    Route::middleware('permission:DisbursementAnalysis')
        ->prefix('disbursement')
        ->name('disbursement.')
        ->group(function () {
            Route::get('/analysis', [DisbursementAnalysisController::class, 'index'])->name('analysis.index');
            Route::get('/analysis/search', [DisbursementAnalysisController::class, 'searchBL'])->name('analysis.search');
            Route::post('/analysis/load', [DisbursementAnalysisController::class, 'loadBL'])->name('analysis.load');
            Route::delete('/analysis/temp', [DisbursementAnalysisController::class, 'clearTemp'])->name('analysis.temp.clear');
            Route::post('/analysis/temp', [DisbursementAnalysisController::class, 'saveTempRow'])->name('analysis.temp.save');
            Route::delete('/analysis/temp/{accountNo}', [DisbursementAnalysisController::class, 'deleteTempRow'])->name('analysis.temp.delete');
            Route::post('/analysis/save', [DisbursementAnalysisController::class, 'save'])->name('analysis.save');
            Route::post('/analysis/reopen', [DisbursementAnalysisController::class, 'reopen'])->name('analysis.reopen');
            Route::post('/analysis/hbl', [DisbursementAnalysisController::class, 'loadHBL'])->name('analysis.hbl.load');
            Route::post('/analysis/temp/add', [DisbursementAnalysisController::class, 'addTempRow'])->name('analysis.temp.add');
        });

    // Gate-Out Expense — requires ConsignmentExpense permission
    Route::middleware('permission:ConsignmentExpense')
        ->prefix('disbursement')
        ->name('disbursement.')
        ->group(function () {
            Route::get('/gate-out', [GateOutExpenseController::class, 'index'])->name('gate-out.index');
            Route::get('/gate-out/consignments', [GateOutExpenseController::class, 'getConsignments'])->name('gate-out.consignments');
            Route::post('/gate-out/save', [GateOutExpenseController::class, 'save'])->name('gate-out.save');
        });

    // Disbursement Approval — requires DisbursementApproval permission
    Route::middleware('permission:DisbursementApproval')
        ->prefix('disbursement')
        ->name('disbursement.')
        ->group(function () {
            Route::get('/approval', [DisbursementApprovalController::class, 'index'])->name('approval.index');
            Route::post('/approval/approve', [DisbursementApprovalController::class, 'approve'])->name('approval.approve');
            Route::post('/approval/decline', [DisbursementApprovalController::class, 'decline'])->name('approval.decline');
            Route::post('/approval/approve-all', [DisbursementApprovalController::class, 'approveAll'])->name('approval.approve-all');
            Route::get('/approval/hbls', [DisbursementApprovalController::class, 'getHBLs'])->name('approval.hbls');
        });

    // Other Expenditure - Admin — requires DisbursementOtherExpense permission
    Route::middleware('permission:DisbursementOtherExpense')
        ->prefix('disbursement')
        ->name('disbursement.')
        ->group(function () {
            Route::get('/other-expenditure', [OtherExpenditureController::class, 'index'])->name('other-expenditure.index');
            Route::get('/other-expenditure/search', [OtherExpenditureController::class, 'searchBL'])->name('other-expenditure.search')->middleware('throttle:60,1');
            Route::post('/other-expenditure/save', [OtherExpenditureController::class, 'save'])->name('other-expenditure.save');
        });
});
