<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Role\RoleController;
use App\Http\Controllers\Admin\CRM\ServiceController;
use App\Http\Controllers\Admin\Account\BankController;
use App\Http\Controllers\Admin\HRM\HRMNoticeController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Report\ReportsController;
use App\Http\Controllers\Admin\Expense\ExpenseController;
use App\Http\Controllers\Admin\Project\ProjectController;
use App\Http\Controllers\Admin\Revenue\RevenueController;
use App\Http\Controllers\Admin\Role\PermissionController;
use App\Models\Inventory\Products\RawMeterial\Rawmaterial;
use App\Http\Controllers\Admin\Account\Loan\LoanController;

use App\Http\Controllers\Admin\CRM\Client\ClientController;

use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Project\Task\TaskController;
use App\Http\Controllers\Admin\Settings\PriorityController;
use App\Http\Controllers\Admin\Settings\ReferenceController;
use App\Http\Controllers\Admin\Account\BankAccountController;
use App\Http\Controllers\Admin\Employee\DepartmentController;
use App\Http\Controllers\Admin\Employee\Leeds\LeedController;
use App\Http\Controllers\Admin\Inventory\InventoryController;
use App\Http\Controllers\Admin\Inventory\Sale\SaleController;
use App\Http\Controllers\Admin\Project\ProjectLinkController;
use App\Http\Controllers\Admin\Project\ProjectTypeController;
use App\Http\Controllers\Admin\Account\FundTransferController;
use App\Http\Controllers\Admin\Employee\DesignationController;
use App\Http\Controllers\Admin\HRM\SalaryManagementController;
use App\Http\Controllers\Admin\HRM\Settings\HolidayController;
use App\Http\Controllers\Admin\HRM\Settings\WeekendController;
use App\Http\Controllers\Admin\Account\CashIn\CashInController;
use App\Http\Controllers\Admin\CRM\Client\ClientTypeController;
use App\Http\Controllers\Admin\Project\ProjectModuleController;
use App\Http\Controllers\Admin\Settings\Address\CityController;
use App\Http\Controllers\admin\Employee\Salary\SalaryController;
use App\Http\Controllers\Admin\HRM\Settings\AllowanceController;
use App\Http\Controllers\Admin\HRM\Settings\PaidLeaveController;
use App\Http\Controllers\Admin\Project\ProjectAccountController;
use App\Http\Controllers\Admin\Project\ProjectReceiptController;
use App\Http\Controllers\Admin\Settings\Address\StateController;

use App\Http\Controllers\Admin\Account\Deposit\DepositController;
use App\Http\Controllers\Admin\Account\Loan\LoanReturnController;
use App\Http\Controllers\Admin\CRM\Client\InterestedOnController;
use App\Http\Controllers\Admin\Project\ProjectAssignToController;
use App\Http\Controllers\Admin\Project\ProjectDocumentController;
use App\Http\Controllers\Admin\Project\ProjectDurationController;
use App\Http\Controllers\Admin\Project\ProjectResourceController;
use App\Http\Controllers\Admin\Settings\Address\CountryController;
use App\Http\Controllers\Admin\Account\Withdraw\WithdrawController;
use App\Http\Controllers\Admin\CRM\Client\ClientBusinessController;
use App\Http\Controllers\Admin\CRM\Client\ClientDocumentController;
use App\Http\Controllers\Admin\CRM\Client\ClientIdentityController;
use App\Http\Controllers\Admin\CRM\Client\ContactThroughController;
use App\Http\Controllers\Admin\Employee\EmployeeDocumentController;
use App\Http\Controllers\Admin\Employee\EmployeeIdentityController;
use App\Http\Controllers\Admin\Expense\ExpenseCategoriesController;
use App\Http\Controllers\Admin\Project\ProjectCategoriesController;
// Inventory Start
use App\Http\Controllers\Admin\Revenue\RevenueCategoriesController;
use App\Http\Controllers\Admin\Account\Loan\LoanAuthorityController;
use App\Http\Controllers\Admin\CRM\Client\ClientReferenceController;
use App\Http\Controllers\Admin\Employee\EmployeeReferenceController;
use App\Http\Controllers\Admin\Inventory\Products\ProductController;
use App\Http\Controllers\Admin\Settings\DashboardSettingsController;
use App\Http\Controllers\Admin\Settings\Identity\IdentityController;
// Inventory End

// Project Start
use App\Http\Controllers\Admin\Account\Investment\InvestorController;
use App\Http\Controllers\Admin\CRM\Client\ClientBankAccontController;
// Project End

// HRM Start
use App\Http\Controllers\Admin\CRM\Client\CommentedClientsController;
use App\Http\Controllers\Admin\Employee\EmployeeBankAccontController;
use App\Http\Controllers\Admin\CRM\Client\Reminder\ReminderController;
use App\Http\Controllers\Admin\Employee\EmployeeCertificateController;
use App\Http\Controllers\Admin\Account\Investment\InvestmentController;
// HRM End

// Inventory Start
use App\Http\Controllers\Admin\Employee\EmployeeQualificationController;
use App\Http\Controllers\Admin\Account\Transaction\TransactionController;
use App\Http\Controllers\Admin\Employee\EmployeeWorkExperienceController;
use App\Http\Controllers\Admin\Inventory\Return\PurchaseReturnController;
use App\Http\Controllers\Admin\Inventory\Settings\InventoryTaxController;

use App\Http\Controllers\Admin\CRM\Client\Comment\ClientCommentController;
use App\Http\Controllers\Admin\Inventory\Settings\InventoryAreaController;
use App\Http\Controllers\Admin\Inventory\Settings\InventoryUnitController;
use App\Http\Controllers\Admin\Account\BalanceSheet\BalanceSheetController;
use App\Http\Controllers\Admin\Inventory\Settings\InventoryBrandController;
use App\Http\Controllers\Admin\Inventory\Purchase\PriceManagementController;
use App\Http\Controllers\Admin\Account\Investment\InvestmentReturnController;
use App\Http\Controllers\Admin\Inventory\Services\InventoryServiceController;
use App\Http\Controllers\Admin\CRM\Client\ClientImport\ClientImportController;
use App\Http\Controllers\Admin\Inventory\Customers\InventoryCustomerController;
use App\Http\Controllers\Admin\Inventory\Settings\InventoryWarehouseController;
use App\Http\Controllers\Admin\Inventory\Suppliers\InventorySupplierController;
use App\Http\Controllers\Admin\CRM\Client\ContactPerson\ContactPersonController;
use App\Http\Controllers\Admin\Account\Loan\Authority\AuthorityDocumentController;
use App\Http\Controllers\Admin\Account\Loan\Authority\AuthorityIdentityController;
use App\Http\Controllers\Admin\CRM\Client\ClientAssignto\ClientAssignToController;
use App\Http\Controllers\Admin\Inventory\Products\RawMaterial\ProductionController;
use App\Http\Controllers\Admin\Inventory\Products\RawMaterial\RawMaterialController;
use App\Http\Controllers\Admin\Account\Investment\Investor\InvestorDocumentController;
use App\Http\Controllers\Admin\Account\Investment\Investor\InvestorIdentityController;
use App\Http\Controllers\Admin\Inventory\Products\InventoryProductCategoriesController;
use App\Http\Controllers\Admin\Inventory\Services\InventoryServiceCategoriesController;
use App\Http\Controllers\Admin\HRM\Settings\AllowanceController as SettingsAllowanceController;

use App\Http\Controllers\Admin\Inventory\Purchase\PurchaseController;
use App\Http\Controllers\Admin\Employee\Salary\SalaryReportController;
use App\Http\Controllers\Admin\Inventory\Products\DamageProductController;
use App\Http\Controllers\Admin\Inventory\Return\SaleReturnController;
use App\Http\Controllers\Admin\Inventory\Return\WholeSaleReturnController;
use App\Http\Controllers\Admin\Inventory\Wholesale\WholesaleController;
use App\Http\Controllers\Admin\Profit\ProfitController;


// use App\Models\Inventory\Products\RawMeterial\Rawmaterial;

// Inventory End


Route::get('/',DashboardController::class)->name('dashboard');

Route::group(['prefix' => 'expense', 'as' => 'expense.'], function () {
    Route::resource('category', ExpenseCategoriesController::class);
    Route::get('category/status/{id}', [ExpenseCategoriesController::class, 'statusUpdate'])->name('category.status');
    Route::resource('expense', ExpenseController::class);
    Route::post('employee/search', [ExpenseController::class, 'employeeSearch'])->name('employee.search');
    Route::get('approve/status/{id}', [ExpenseController::class, 'statusUpdate'])->name('approve.status');
    Route::get('bill/print/{id}', [ExpenseController::class, 'printInvoice'])->name('print.invoice');
    Route::get('voucher/print/{id}', [ExpenseController::class, 'printVoucher'])->name('print.voucher');
});

//revenue
Route::resource('revenue', RevenueController::class);
Route::group(['prefix' => 'revenue', 'as' => 'revenue.'], function () {
    Route::get('status/{id}', [RevenueController::class, 'statusUpdate'])->name('update.status');
    Route::get('approve/status/{id}', [RevenueController::class, 'statusUpdate'])->name('approve.status');
    Route::get('bill/print/{id}', [RevenueController::class, 'printInvoice'])->name('print.invoice');
    Route::get('voucher/print/{id}', [RevenueController::class, 'printVoucher'])->name('print.voucher');

});
//revenue category
Route::resource('revenue-category', RevenueCategoriesController::class);
Route::group(['prefix' => 'revenue-category', 'as' => 'revenue-category.'], function () {
    Route::get('status/{id}', [RevenueCategoriesController::class, 'statusUpdate'])->name('update.status');
});

//Commented Client
Route::resource('commented-client', CommentedClientsController::class);

Route::group(['prefix' => 'crm', 'as' => 'crm.'], function () {
    //contact through
    Route::resource('contact-through', ContactThroughController::class);
    Route::group(['prefix' => 'contact-through', 'as' => 'contact-through.'], function () {
        Route::get('status/{id}', [ContactThroughController::class, 'statusUpdate'])->name('update.status');
    });

    //interested on
    Route::resource('interested-on', InterestedOnController::class);
    Route::group(['prefix' => 'interested-on', 'as' => 'interested-on.'], function () {
        Route::get('status/{id}', [InterestedOnController::class, 'statusUpdate'])->name('update.status');
    });

    //client type
    Route::resource('client-type', ClientTypeController::class);
    Route::group(['prefix' => 'client-type', 'as' => 'client-type.'], function () {
        Route::get('status/{id}', [ClientTypeController::class, 'statusUpdate'])->name('update.status');
    });

    //client business Category
    Route::resource('client-business-category', ClientBusinessController::class);
    Route::group(['prefix' => 'client-business-category', 'as' => 'client-business-category.'], function () {
        Route::get('status/{id}', [ClientBusinessController::class, 'statusUpdate'])->name('update.status');
    });

    //client
    Route::resource('client', ClientController::class);
    Route::post('client-search-all', [ClientController::class, 'ClientSearch'])->name('client.search.all');

    Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
        Route::post('client', [ClientController::class, 'import'])->name('client');
        Route::get('export', [ClientController::class, 'export'])->name('export');
    });

    Route::get('client-type-priority/{id}', [ClientController::class, 'getClientTypePriority'])->name('client.type.priority');
    Route::post('client-address-update/{id}', [ClientController::class, 'ClientAddressUpdate'])->name('address.update');
    Route::group(['prefix' => 'client', 'as' => 'client.'], function () {
        Route::get('status/{id}', [ClientController::class, 'statusUpdate'])->name('update.status');
        Route::get('profile/{id}', [ClientController::class, 'clientProfile'])->name('profile');
        Route::post('client-list/{b?}/{c?}', [ClientController::class, 'ClientList'])->name('list');
        Route::post('business-category-search', [ClientController::class, 'BusinesCategory'])->name('busines.category');
        Route::post('client-type-search', [ClientController::class, 'ClientType'])->name('type.search');
    });

    //client all details route
    Route::resource('client-document', ClientDocumentController::class);
    Route::resource('client-identity', ClientIdentityController::class);
    Route::resource('client-reference', ClientReferenceController::class);

    //client Comment
    Route::group(['prefix' => 'comment', 'as' => 'comment.'], function () {
        Route::get('edit/{id}/{parameter}', [ClientCommentController::class, 'commentEdit'])->name('edit');
        Route::put('update/{id}/{parameter}', [ClientCommentController::class, 'commentUpdate'])->name('update');
    });
    Route::get('comment/{id}', [ClientController::class, 'clientComment'])->name('client.comment');
    Route::resource('client-comment', ClientCommentController::class);
    Route::get('comment/status/{id}', [ClientCommentController::class, 'statusUpdate'])->name('comment.update.status');
    Route::post('client-search', [ClientCommentController::class, 'ClientSearch'])->name('client.search');

    //Reminder Route
    Route::group(['prefix' => 'reminder', 'as' => 'reminder.'], function () {
        Route::get('edit/{id}/{parameter}', [ReminderController::class, 'reminderEdit'])->name('edit');
        Route::put('update/{id}/{parameter}', [ReminderController::class, 'reminderUpdate'])->name('update');
        Route::post('employee/search', [ReminderController::class, 'employeeSearch'])->name('employee-search');
    });

    Route::get('reminder/{id}', [ClientController::class, 'clientReminder'])->name('client.reminder');
    Route::resource('client-reminder', ReminderController::class);
    Route::post('client-reminder-today', [ReminderController::class, 'todayReminder'])->name('client-reminder-today');

    Route::get('reminder/status/{id}', [ReminderController::class, 'statusUpdate'])->name('reminder.update.status');
    //client bank account
    Route::resource('client-bank-account', ClientBankAccontController::class);
    Route::get('client/bank/status/{id}', [ClientBankAccontController::class, 'statusUpdate'])->name('client.bank.update.status');

    //AssignTo
    Route::get('client-assign/{id}', [ClientAssignToController::class, 'clientAssignTo'])->name('client.client.assignto');
    Route::resource('client-assignto', ClientAssignToController::class);
    Route::group(['prefix' => 'client', 'as' => 'client.'], function () {
        Route::post('assign-to/{id}', [ClientAssignToController::class, 'AssignToDelete'])->name('assignto.delete');
        Route::get('edit/{id}/{parameter}', [ClientBankAccontController::class, 'bankAccountEdit'])->name('bank.account.edit');
        Route::put('update/{id}/{parameter}', [BankAccountController::class, 'bankAccountUpdate'])->name('bank.account.update');
        // contact person
        Route::resource('contact-person', ContactPersonController::class);
    });
    Route::post('all-employee-search', [ClientAssignToController::class, 'AllEmployeeSearch'])->name('allemplyee.search');
});



//Employee
Route::resource('employee', EmployeeController::class);
Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
        Route::post('employee', [EmployeeController::class, 'import'])->name('employee');
        Route::get('export', [EmployeeController::class, 'export'])->name('export');
    });
Route::group(['prefix' => 'employee-profile', 'as' => 'employee-profile.'], function () {
        Route::get('pdf', [EmployeeController::class, 'employeePdf'])->name('pdf');
    });
Route::resource('employees-identity', EmployeeIdentityController::class);
Route::resource('employees-document', EmployeeDocumentController::class);
Route::resource('employee-qualification', EmployeeQualificationController::class);
Route::resource('employee-work-experience', EmployeeWorkExperienceController::class);
Route::resource('employee-certificate', EmployeeCertificateController::class);
Route::resource('employee-reference', EmployeeReferenceController::class);
Route::resource('employee-bank-account', EmployeeBankAccontController::class);
Route::resource('employee-leeds', LeedController::class);

Route::get('employee-work-report', [EmployeeController::class, 'workReport'])->name('employee.report.crm');

Route::get('employee/bank/status/{id}', [EmployeeBankAccontController::class, 'statusUpdate'])->name('employee.bank.update.status');
Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
    Route::get('status/{id}', [EmployeeController::class, 'statusUpdate'])->name('update.status');
    Route::get('profile/{id}', [EmployeeController::class, 'employeeProfile'])->name('profile');
    Route::post('details-address-country-search', [EmployeeController::class, 'countrySearch'])->name('details.address.country.search');
    Route::get('details-address-state-search', [EmployeeController::class, 'stateSearch'])->name('details.address.state.search');
    Route::get('details-address-city-search', [EmployeeController::class, 'citySearch'])->name('details.address.city.search');
    Route::post('details-department-search', [EmployeeController::class, 'departmentSearch'])->name('details.department.search');
    Route::post('details-designation-search', [EmployeeController::class, 'designationSearch'])->name('details.designation.search');
    Route::post('details-reference-search', [EmployeeController::class, 'referenceSearch'])->name('details.reference.search');
    Route::post('employee-address-update/{id}', [EmployeeController::class, 'EmployeeAddressUpdate'])->name('address.update');
//leeds
    Route::post('employee-own-leeds', [LeedController::class, 'ownLeeds'])->name('employee-own-leeds');
    Route::get('employee-assign-leeds/{id}', [LeedController::class, 'assignLeeds'])->name('employee-assign-leeds');



});

//Department
Route::resource('department', DepartmentController::class);
Route::group(['prefix' => 'department', 'as' => 'department.'], function () {
    Route::get('status/{id}', [DepartmentController::class, 'statusUpdate'])->name('update.status');
});

//Designation
Route::resource('designation', DesignationController::class);
Route::group(['prefix' => 'designation', 'as' => 'designation.'], function () {
    Route::get('status/{id}', [DesignationController::class, 'statusUpdate'])->name('update.status');
});


Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    //role
    Route::resource('role', RoleController::class);
    Route::post('all-role', [RoleController::class, 'role'])->name('all-role');

    //permission
    Route::resource('permission', PermissionController::class);

    //Reference
    Route::resource('reference', ReferenceController::class);
    Route::group(['prefix' => 'reference', 'as' => 'reference.'], function () {
        Route::get('status/{id}', [ReferenceController::class, 'statusUpdate'])->name('update.status');
    });

    //Priority
    Route::resource('priority', PriorityController::class);
    Route::group(['prefix' => 'priority', 'as' => 'priority.'], function () {
        Route::get('status/{id}', [PriorityController::class, 'statusUpdate'])->name('update.status');
    });

    //Dashboard
    Route::get('dashboard', [DashboardSettingsController::class, 'create'])->name('dashboard.create');
    Route::post('dashboard/store', [DashboardSettingsController::class, 'store'])->name('dashboard.store');

    //identity
    Route::resource('identity', IdentityController::class);
    Route::group(['prefix' => 'identity', 'as' => 'identity.'], function () {
        Route::get('status/{id}', [IdentityController::class, 'statusUpdate'])->name('update.status');
    });
    Route::post('identity-search', [EmployeeController::class, 'identitySearch'])->name('identity.search');
});

// Address Route
Route::group(['prefix' => 'address', 'as' => 'address.'], function () {
    Route::resource('country', CountryController::class);
    Route::resource('state', StateController::class);
    Route::resource('city', CityController::class);
});
Route::resource('raw-material', RawMaterialController::class);
Route::group(['prefix' => 'raw-material', 'as' => 'raw-material.'], function () {
    Route::get('status/{id}', [RawMaterialController::class, 'statusUpdate'])->name('update.status');
});
Route::group(['prefix' => 'production', 'as' => 'production.'], function () {
    Route::resource('/', ProductionController::class);
    Route::post('rawmaterial', [ProductionController::class, 'rawmaterial'])->name('rawmaterial');
});

//investor route
Route::resource('investor', InvestorController::class);
Route::group(['prefix' => 'investor', 'as' => 'investor.'], function () {
    Route::get('status/{id}', [InvestorController::class, 'statusUpdate'])->name('update.status');
    Route::post('address-update/{id}', [InvestorController::class, 'InvestorAddressUpdate'])->name('address.update');
    Route::resource('document', InvestorDocumentController::class);
    Route::resource('identity', InvestorIdentityController::class);

});

//investment route
Route::resource('investment', InvestmentController::class);
Route::resource('investment-return', InvestmentReturnController::class);
Route::group(['prefix' => 'investment', 'as' => 'investment.'], function () {
    Route::get('list/{id}', [InvestmentController::class, 'InvestmentList'])->name('list');
    Route::get('invoice/{id}', [InvestmentController::class, 'invoice'])->name('invoice');
    Route::get('invoice-return/{id}', [InvestmentReturnController::class, 'returnInvoice'])->name('return.invoice');
    Route::get('invoice-profit-return/{id}', [InvestmentController::class, 'ProfitReturnInvoice'])->name('profit.return.invoice');
    Route::post('investor/search', [InvestmentController::class, 'investorSearch'])->name('investor.search');
    Route::post('employee/search', [InvestmentController::class, 'employeeSearch'])->name('employee.search');
});

//loan route
Route::resource('loan', LoanController::class);
Route::resource('loan-return', LoanReturnController::class);
Route::resource('loan-authority', LoanAuthorityController::class);
Route::group(['prefix' => 'loan', 'as' => 'loan.'], function () {
    Route::get('list/{id}', [LoanController::class, 'LoanList'])->name('list');
    Route::get('status/list', [LoanController::class, 'LoanListStatus'])->name('list.status');
    Route::get('loan-invoice/{id}', [LoanController::class, 'LoanInvoice'])->name('invoice');
    Route::get('loan-return-invoice/{id}', [LoanReturnController::class, 'LoanReturnInvoice'])->name('return.invoice');
    Route::get('details/{id}', [LoanController::class, 'LoanDetails'])->name('details');
    Route::post('authority-address-update/{id}', [LoanAuthorityController::class, 'AuthorAddressUpdate'])->name('author.address.update');
    Route::post('authority/search', [LoanController::class, 'authorSearch'])->name('author.search');
    //authority route
    Route::group(['prefix' => 'authority', 'as' => 'authority.'], function () {
        Route::resource('document', AuthorityDocumentController::class);
        Route::resource('identity', AuthorityIdentityController::class);
    });
});
Route::group(['prefix' => 'loan-return', 'as' => 'loan-return.'], function () {
    Route::get('list/{id}', [LoanReturnController::class, 'LoanReturnList'])->name('list');
});

//Bank
Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
    Route::resource('bank', BankController::class);
    Route::group(['prefix' => 'bank', 'as' => 'bank.'], function () {
        Route::get('status/{id}', [BankController::class, 'statusUpdate'])->name('update.status');
    });
    Route::resource('bank-account', BankAccountController::class);
    Route::get('bank-account-report/{id}', [BankAccountController::class,'Transaction'])->name('bank-account.report');
    Route::get('bank-account-revenue/{id}', [BankAccountController::class,'Revenue'])->name('bank-account.revenue');
    Route::get('bank-account-expense/{id}', [BankAccountController::class,'Expense'])->name('bank-account.expense');

    Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
        Route::get('status/{id}', [BankAccountController::class, 'statusUpdate'])->name('update.status');
    });

    //transaction route
    Route::resource('transaction', TransactionController::class);
    Route::group(['prefix' => 'transaction', 'as' => 'transaction.'], function () {
        Route::get('status/{id}', [TransactionController::class, 'statusUpdate'])->name('update.status');
    });
    // deposit
    Route::resource('deposit', DepositController::class);
    Route::group(['prefix' => 'deposit', 'as' => 'deposit.'], function () {
        Route::post('bankAccount/search', [DepositController::class, 'bankAccountSearch'])->name('bank.search');
    });
    Route::resource('withdraw', WithdrawController::class);
    Route::resource('cash-in', CashInController::class);

    //bank balance sheet
    Route::get('balance-sheet', [BalanceSheetController::class, 'index'])->name('balance.sheet.index');
    Route::get('balance-sheet-data', [BalanceSheetController::class, 'balanceSheetData'])->name('balanceSheetData');
    Route::post('account-statement/data', [BalanceSheetController::class, 'accountStatementData'])->name('statement.data');
    Route::get('monthly-balance-sheet', [BalanceSheetController::class, 'monthlyBalanceSheet'])->name('monthly.balance.sheet');
    Route::post('statement-bank-account-search', [BalanceSheetController::class, 'statementBankAccountSearch'])->name('statement.account.search');

    //get cash balance sheet
    Route::get('cash-balance-sheet', [BalanceSheetController::class, 'cashBalanceSheet'])->name('cash.balance.sheet.index');
    Route::group(['prefix' => 'bank.account', 'as' => 'bank.account.'], function () {
        Route::get('balance/{id}', [BalanceSheetController::class, 'bankAccountBalance'])->name('balance');
        Route::get('statement', [BalanceSheetController::class, 'bankAccountStatement'])->name('Statement');
        Route::post('monthly-balance-sheet-data', [BalanceSheetController::class, 'monthlyBalanceSheetData'])->name('monthly.balance.sheet.data');
        Route::post('monthly-balance-sheet-data-print', [BalanceSheetController::class, 'monthlyBalanceSheetDataPrint'])->name('monthly.balance.sheet.data.print');
    });

    //fund transafer
    Route::resource('fund-transfer', FundTransferController::class);
    Route::group(['prefix' => 'fund-transfer', 'as' => 'fund-transfer.'], function () {
        Route::post('fund-transfer/bankaccount/search', [FundTransferController::class, 'bankAccountSearch'])->name('bank.account.search');
    });
});

//Inventory
//
Route::group(['prefix' => 'inventory', 'as' => 'inventory.'], function () {
    Route::resource('wholesale', WholesaleController::class);
    Route::group(['prefix' => 'wholesale', 'as' => 'wholesale.'], function () {
        Route::get('wholesale/{id}', [ WholesaleController::class, 'wholeSaleProductSearch'])->name('wholesale.search');
        Route::post('wholesale/payement/receive', [WholesaleController::class, 'receivedPayment'])->name('received.payment');

        Route::get('product/data/{id}', [WholesaleController::class, 'productData'])->name('product.data');

        Route::get('history/{id}', [WholesaleController::class, 'wholesaleHistory'])->name('history');

        Route::delete('history/destroy/{id}', [WholesaleController::class, 'saleHistoryDelete'])->name('history.destroy');
        Route::get('data/{id}', [WholesaleController::class,'saleData'])->name('data');
        Route::put('update.wholesale/{id}', [WholesaleController::class,'updateSale'])->name('update.wholesale');

    });

    Route::resource('product-count', InventoryController::class);
    Route::resource('damage-product', DamageProductController::class);

    Route::group(['prefix' => 'damage-product', 'as' => 'damage-product.'], function () {
    Route::post('product/search', [DamageProductController::class, 'productSearch'])->name('product.search');
    });
    Route::group(['prefix' => 'product-count', 'as' => 'product-count.'], function () {
        Route::get('details/{id}/{variant?}', [InventoryController::class, 'inevntoryDetails'])->name('inventory.details');
    });
    // Settings
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        //Unit
        Route::resource('unit', InventoryUnitController::class);
        Route::group(['prefix' => 'unit', 'as' => 'unit.'], function () {
            Route::get('status/{id}', [InventoryUnitController::class, 'statusUpdate'])->name('update.status');
        });
        //area
        Route::resource('area', InventoryAreaController::class)->except('show');
        Route::group(['prefix' => 'area', 'as' => 'area.'], function () {
            Route::get('status/{id}', [InventoryAreaController::class, 'statusUpdate'])->name('update.status');
        });
        //Warehouse
        Route::resource('warehouse', InventoryWarehouseController::class);
        Route::group(['prefix' => 'warehouse', 'as' => 'warehouse.'], function () {
            Route::get('status/{id}', [InventoryWarehouseController::class, 'statusUpdate'])->name('update.status');
            Route::post('search', [InventoryWarehouseController::class, 'warehouseSearch'])->name('search');
        });
        //Brand
        Route::resource('brand', InventoryBrandController::class);
        Route::group(['prefix' => 'brand', 'as' => 'brand.'], function () {
            Route::get('status/{id}', [InventoryBrandController::class, 'statusUpdate'])->name('update.status');
        });
        //ProductCategory
        Route::resource('productCategory', InventoryProductCategoriesController::class);
        Route::group(['prefix' => 'productCategory', 'as' => 'productCategory.'], function () {
            Route::get('status/{id}', [InventoryProductCategoriesController::class, 'statusUpdate'])->name('update.status');
        });

        //tax
        Route::resource('tax', InventoryTaxController::class);
        Route::group(['prefix' => 'tax', 'as' => 'tax.'], function () {
            Route::get('status/{id}', [InventoryTaxController::class, 'statusUpdate'])->name('update.status');
        });
    });

    Route::resource('product', ProductController::class);
    Route::get('product/transaction/{id}', [ProductController::class,'ProductTransaction'])->name('product.transaction');
    // Products
    Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
        //ProductCategory
        Route::resource('category', InventoryProductCategoriesController::class);
        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('status/{id}', [InventoryProductCategoriesController::class, 'statusUpdate'])->name('update.status');
        });
        Route::get('gencode', [ProductController::class,'generateCode'])->name('gencode');
        Route::get('unit-search/{id}', [ProductController::class,'unitSearch'])->name('unit.search');
        Route::post('brand-search', [ProductController::class,'brandSearch'])->name('brand.search');
    });

    Route::resource('purchase', PurchaseController::class);
    Route::resource('price-management', PriceManagementController::class);
    Route::post('product/search', [PriceManagementController::class, 'productSearch'])->name('price.management.product.search');
    Route::get('product/price/{id}', [PriceManagementController::class, 'productPriceSearch'])->name('product.price.search');
    Route::group(['prefix' => 'purchase', 'as' => 'purchase.'], function () {
        Route::post('product/search', [PurchaseController::class, 'productSearch'])->name('product.search');
        Route::post('supplier/search', [PurchaseController::class, 'supplierSearch'])->name('supplier.search');
        Route::get('product/data/{id}', [PurchaseController::class, 'productData'])->name('product.data');
        Route::get('warehouse/search/{id}', [PurchaseController::class, 'warehouseSearch'])->name('warehouse.search');
        Route::post('addpayment', [PurchaseController::class, 'addPayment'])->name('add.payment');
        Route::get('search/{id}', [PurchaseController::class, 'purchaseSearch'])->name('search');
        Route::get('history/{id}', [PurchaseController::class, 'purchaseHistory'])->name('history');
        Route::delete('history/destroy/{id}', [PurchaseController::class, 'purchaseHistoryDelete'])->name('history.destroy');
        Route::get('data/{id}', [PurchaseController::class,'purchaseData'])->name('data');

        Route::put('update.purchase/{id}', [PurchaseController::class,'updatePurchase'])->name('update.purchase');
    });
    Route::resource('purchase-return', PurchaseReturnController::class);
    Route::group(['prefix' => 'purchase-return', 'as' => 'purchase-return.'], function () {
        Route::post('product/search', [PurchaseReturnController::class,'productSearch'])->name('product.search');
        Route::get('invoice/search/{id}', [PurchaseReturnController::class,'purchaseProductSearch'])->name('purchase.search');
        Route::get('product/quantity/search', [PurchaseReturnController::class,'quantitySearch'])->name('quantity.search');
    });

    // sale
    Route::resource('sale', SaleController::class);
    Route::post('sale/payement/receive', [SaleController::class, 'receivedPayment'])->name('received.payment');
    Route::group(['prefix' => 'sale', 'as' => 'sale.'], function () {
        Route::post('customer/search', [SaleController::class, 'customerSearch'])->name('customer.search');
        Route::post('product/search', [SaleController::class, 'productSearch'])->name('product.search');
        Route::get('payment/search/{id}', [SaleController::class, 'saleSearch'])->name('search');
        Route::get('product/quantity/search', [SaleController::class, 'quantitySearch'])->name('quantity.search');
        Route::get('product/data/{id}', [SaleController::class, 'productData'])->name('product.data');
        Route::get('invoice/search/{id}', [SaleController::class,'saleProductSearch'])->name('sale.search');

        Route::get('history/{id}', [SaleController::class, 'saleHistory'])->name('history');
        Route::delete('history/destroy/{id}', [SaleController::class, 'saleHistoryDelete'])->name('history.destroy');
        Route::get('data/{id}', [SaleController::class,'saleData'])->name('data');
        Route::put('update.sale/{id}', [SaleController::class,'updateSale'])->name('update.sale');
    });

    Route::resource('sale-return', SaleReturnController::class);
    Route::group(['prefix' => 'sale-return', 'as' => 'sale-return.'], function () {
        Route::post('product/search', [SaleReturnController::class,'productSearch'])->name('product.search');
        Route::get('product/sale/search/{id}', [SaleReturnController::class,'saleProductSearch'])->name('sale.search');
    });
    Route::resource('whole-sale-return', WholeSaleReturnController::class);
    Route::group(['prefix' => 'whole-sale-return', 'as' => 'whole-sale-return.'], function () {
        Route::post('product/search', [WholeSaleReturnController::class,'productSearch'])->name('product.search');
        Route::get('product/sale/search/{id}', [WholeSaleReturnController::class,'saleProductSearch'])->name('sale.search');
    });



    // Service
    Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
        // Category
        Route::resource('category', InventoryServiceCategoriesController::class);
        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('status/{id}', [InventoryServiceCategoriesController::class, 'statusUpdate'])->name('update.status');
        });

        //Service
        Route::resource('service', InventoryServiceController::class);
        Route::group(['prefix' => 'service', 'as' => 'service.'], function () {
            Route::get('status/{id}', [InventoryServiceController::class, 'statusUpdate'])->name('update.status');
        });
    });

    // Customers
    Route::group(['prefix' => 'customers', 'as' => 'customers.'], function () {
        //customer
        Route::get('country-wise-state', [InventoryCustomerController::class,'CountryWiseState'])->name('country-wise-state');
        Route::get('state-wise-city', [InventoryCustomerController::class,'StateWiseCity'])->name('state-wise-city');

        Route::get('client-type-priority/{id}', [InventoryCustomerController::class, 'getCustomerTypePriority'])->name('customer.type.priority');
        Route::resource('customer', InventoryCustomerController::class);
        Route::get('customer/view/{id}', [InventoryCustomerController::class, 'view'])->name('customer.view');
        Route::get('customer/due/{id}', [InventoryCustomerController::class, 'DueList'])->name('customer.due');
        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
            Route::get('status/{id}', [InventoryCustomerController::class, 'statusUpdate'])->name('update.status');
        });
    });

    // Suppliers
    Route::group(['prefix' => 'suppliers', 'as' => 'suppliers.'], function () {
        Route::resource('supplier', InventorySupplierController::class);
        Route::get('supplier/pay/{id}', [InventorySupplierController::class, 'PayList'])->name('supplier.pay');
        Route::get('status/{id}', [InventorySupplierController::class, 'statusUpdate'])->name('update.status');
    });
});


//Project

Route::resource('projects', ProjectController::class);
Route::group(['prefix' => 'projects', 'as' => 'projects.'], function () {
    Route::post('category/search', [ProjectController::class, 'projectCategorySearch'])->name('category.search');
    Route::post('employee-search', [ProjectController::class, 'employeeSearch'])->name('employee.search');
});

Route::group(['prefix' => 'project', 'as' => 'project.'], function () {
    Route::resource('document', ProjectDocumentController::class);
    Route::get('hold/{id}', [ProjectController::class, 'projectHold'])->name('hold');
    Route::get('un-hold/{id}', [ProjectController::class, 'projectUnHold'])->name('unhold');
    Route::get('hold-history/{id}', [ProjectController::class, 'holdList'])->name('hold.list');
    Route::resource('link', ProjectLinkController::class);
    Route::group(['prefix' => 'link', 'as' => 'link.'], function () {
        Route::get('details/{id}', [ProjectLinkController::class, 'linkDetails'])->name('details');
    });

    //Task
    Route::resource('task', TaskController::class);
    Route::group(['prefix' => 'task', 'as' => 'task.'], function () {
        Route::get('task/{id}', [TaskController::class, 'taskDetails'])->name('view');
    });

    Route::resource('duration', ProjectDurationController::class);
    Route::group(['prefix' => 'duration', 'as' => 'duration.'], function () {
        Route::get('details/{id}', [ProjectDurationController::class, 'durationDetails'])->name('details');
        Route::get('hold-list/{id}', [ProjectDurationController::class, 'durationHoldList'])->name('hold.list');
    });
    Route::get('project-duration/{id}', [ProjectDurationController::class, 'projectDuration'])->name('duration');

    //Resource
    Route::resource('resource', ProjectResourceController::class);
    Route::group(['prefix' => 'resource', 'as' => 'resource.'], function () {
        Route::get('details/{id}', [ProjectResourceController::class, 'resourceDetails'])->name('details');
    });

    Route::get('project-resource/{id}', [ProjectResourceController::class, 'projectResource'])->name('resource');
    // Resource End

    Route::resource('module', ProjectModuleController::class);
    Route::group(['prefix' => 'module', 'as' => 'module.'], function () {
        Route::get('show/{id}', [ProjectModuleController::class, 'projectModule'])->name('show-all');
        Route::post('edit', [ProjectModuleController::class, 'projectUpdate'])->name('project-update');
        Route::get('hold/{id}', [ProjectModuleController::class, 'moduleHold'])->name('hold');
        Route::get('un-hold/{id}', [ProjectModuleController::class, 'moduleUnHold'])->name('unhold');
        Route::get('module-add/{id}', [ProjectModuleController::class, 'create'])->name('add');
        Route::post('module-search', [ProjectModuleController::class, 'moduleSearch'])->name('search.module');
        Route::get('hold-history/{id}', [ProjectModuleController::class, 'holdList'])->name('hold.list');
    });

    Route::get('grid-view', [ProjectController::class, 'gridView'])->name('grid.view');
    Route::resource('assign-to', ProjectAssignToController::class);
    Route::post('employee-search', [ProjectAssignToController::class,'AllEmployeeSearch'])->name('employee.search');
    Route::get('employee-assign/{id}', [ProjectAssignToController::class, 'employeeAssignTo'])->name('employee.assign.to');

    Route::group(['prefix' => 'reporting', 'as' => 'reporting.'], function () {
        Route::post('add-reporting-person', [ProjectAssignToController::class, 'AddReportingEmployee'])->name('assign');
        Route::post('search-reporting-person', [ProjectAssignToController::class, 'ReportingEmployeeSearch'])->name('search');
        Route::get('reporting-person-show/{id}', [ProjectAssignToController::class, 'ReportingEmployeeShow'])->name('show');
        Route::post('reporting-person-delete/{id}', [ProjectAssignToController::class, 'destroyReporting'])->name('delete');
    });

    //Accounts
    Route::resource('account-budget', ProjectAccountController::class);
    Route::group(['prefix' => 'account-budget', 'as' => 'account-budget.'], function () {
        Route::get('show/{id}', [ProjectAccountController::class, 'projectAccounts'])->name('view');
    });

    Route::resource('budget-receipt', ProjectReceiptController::class);
    Route::group(['prefix' => 'budget-receipt', 'as' => 'budget-receipt.'], function () {
        Route::post('document', [ProjectReceiptController::class, 'receiveDocument'])->name('document');
    });

    // category
    Route::resource('category', ProjectCategoriesController::class);
    Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
        Route::get('status/{id}', [ProjectCategoriesController::class, 'statusUpdate'])->name('update.status');
    });

    // type
    Route::resource('type', ProjectTypeController::class);
    Route::group(['prefix' => 'type', 'as' => 'type.'], function () {
        Route::get('status/{id}', [ProjectTypeController::class, 'statusUpdate'])->name('update.status');
    });
});


//HRM
Route::group(['prefix' => 'hrm', 'as' => 'hrm.'], function () {
    //salary
    Route::resource('salary', SalaryManagementController::class);
    // allowance
    Route::resource('allowance', AllowanceController::class);
    Route::post('allowance/status-update/{id}', [AllowanceController::class,'statusUpdate'])->name('allowance.status.update');
    Route::post('allowance-show', [AllowanceController::class,'allowanceShow'])->name('allowance-show');
    Route::post('allowance-update', [AllowanceController::class, 'allowanceUpdate'])->name('allowance-update');
   // paid-leave
   Route::resource('paid-leave', PaidLeaveController::class);
   Route::post('paid-leave-show', [PaidLeaveController::class,'leaveShow'])->name('paid-leave-show');
   Route::post('paid-leave/status-update/{id}', [PaidLeaveController::class,'statusUpdate'])->name('paid-leave.status');
   Route::post('paid-leave-update', [PaidLeaveController::class, 'paidLeaveUpdate'])->name('paid-leave-update');
    // notice
    Route::post('department/search', [HRMNoticeController::class, 'departmentSearch'])->name('department.search');
    Route::post('department/employee', [HRMNoticeController::class, 'getDepartmentWiseEmployee'])->name('getDepartmentWiseEmployee');
    Route::post('department/dept', [HRMNoticeController::class, 'getDepartment'])->name('getDepartment');
    Route::resource('notice', HRMNoticeController::class);
    Route::group(['prefix' => 'notice', 'as' => 'notice.'], function () {
        Route::get('status/{id}', [HRMNoticeController::class, 'statusUpdate'])->name('update.status');
    });

    // Settings
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        // Holiday
        Route::resource('holiday', HolidayController::class);
        Route::group(['prefix' => 'holiday', 'as' => 'holiday.'], function () {
            Route::get('status/{id}', [HolidayController::class, 'statusUpdate'])->name('update.status');
        });

        // Weekend
        Route::resource('weekend', WeekendController::class);
        Route::group(['prefix' => 'weekend', 'as' => 'weekend.'], function () {
            Route::get('status/{id}', [WeekendController::class, 'statusUpdate'])->name('update.status');
        });
    });

});

//ALLOWANCE

Route::get('project/ongoing', function () {
    $project = \App\Models\Project\Projects::where('status',2)->latest()->paginate(5);
    return view('admin.project.project.partial.grid.ongoing')->render();
});


//salary
Route::resource('salary', SalaryController::class);
//Route::post('salaryList', [SalaryController::class,'salaryList'])->name('salarylist');
Route::get('salaryList/{id}', [SalaryController::class,'salaryList'])->name('salarylist');
Route::post('salary-show', [SalaryController::class,'salaryShow'])->name('salary-show');
Route::post('salary/statusupdate/{id}', [SalaryController::class,'statusUpdate'])->name('salary.status.update');
Route::post('salary-update/{id}', [SalaryController::class, 'salaryUpdate'])->name('salary-update');

//Salary Report
Route::resource('salaryReport',SalaryReportController::class);
Route::get('salary/edit/{id}',[SalaryReportController::class,'edit'])->name('salary.report.edit');
Route::post('salaryupdate/{id}',[SalaryReportController::class,'update'])->name('salary-report.update');
Route::post('salary/update/{id}',[SalaryController::class,'update'])->name('salary.report.update');
Route::get('salary/report/statusupdate/{id}', [SalaryReportController::class,'statusUpdate'])->name('salaryReport.update.status');
Route::get('employee/salary/report/',[SalaryReportController::class,'reportList'])->name('salary.list');
Route::post('employee/salary/list/',[SalaryReportController::class,'reportListShow'])->name('salary.list.show');
Route::delete('employee/salaryReport/delete/{id}',[SalaryReportController::class,'destroy'])->name('salaryReport.destroy');
Route::get('salary/get-employee/{id}',[SalaryReportController::class,'getEmployee'])->name('employee.by.warehouse');

Route::post('salary/generate',[SalaryReportController::class,'salaryGenerate'])->name('salaryGenerate.store');
Route::get('employee/salary/confirm/{id}',[SalaryReportController::class,'salaryConfirm'])->name('salary.confirm');
Route::post('employee/salary/pay/{id}',[SalaryReportController::class,'store'])->name('salary.pay');
Route::get('employee/salary-confirm/edit/{id}',[SalaryReportController::class,'salaryConfirmEdit'])->name('salary.confirm.edit');
Route::post('employee/salary-confirm/update/{id}',[SalaryReportController::class,'salaryConfirmUpdate'])->name('salary.confirm.update');
Route::get('employee/report/salary/{id}',[SalaryReportController::class,'empByWarehouse'])->name('employee.salary.report.by.warehouse');


//Profit
//Route::resource('profit',ProfitController::class);
Route::get('net/profit',[ProfitController::class,'netProfit'])->name('monthly.net.profit');
Route::get('net/profit/{month}/{warehouse}',[ProfitController::class,'netProfitGet'])->name('net.profit');
Route::get('gross/profit',[ProfitController::class,'grossProfit'])->name('monthly.gross.profit');
Route::get('gross/profit/{month}/{warehouse}',[ProfitController::class,'grossProfitGet'])->name('gross.profit');

//reports
Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
    // customer due list
    Route::get('customer/due/list', [ReportsController::class,'CustomerDueList'])->name('customer.due.list');
    //supplier pay list
    Route::get('supplier/pay/list', [ReportsController::class,'SupplierPayList'])->name('supplier.pay.list');
    //purchase report
    Route::get('purchase/report', [ReportsController::class,'PurchaseReport'])->name('purchase.report');
    //sales report
    Route::get('sales/report', [ReportsController::class,'SalesReport'])->name('sales.report');
    //expense report
    Route::get('expense/report', [ReportsController::class,'ExpenseReport'])->name('expense.report');
    //revenue report
    Route::get('revenue/report', [ReportsController::class,'RevenueReport'])->name('revenue.report');
    //income report
    Route::get('income/report', [ReportsController::class,'IncomeReport'])->name('income.report');
    //wholesale report
    Route::get('wholesale/report', [ReportsController::class,'WholeSaleReport'])->name('wholesale.report');

});
