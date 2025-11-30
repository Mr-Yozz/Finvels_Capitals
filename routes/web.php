<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RepaymentController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AccountCategoryController;
use App\Http\Controllers\AccountDashboardController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CollectionSheetController;


Route::get('/', [BaseController::class, 'base'])->name('base');
Route::get('/about', [BaseController::class, 'about'])->name('about');
Route::get('/service', [BaseController::class, 'service'])->name('service');
Route::get('/contact', [BaseController::class, 'contact'])->name('contact');

Route::get('/showLogin', [AuthController::class, 'showLogin'])->name('home');
// Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin + Manager dashboard access
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserManagementController::class);
});

// Manager-only routes (if you have manager-specific dashboard)
// Route::middleware(['auth', 'role:manager'])->group(function () {
//     Route::get('/manager/dashboard', [DashboardController::class, 'manager'])->name('manager.dashboard');
// });

Route::middleware(['auth'])->group(function () {

    // Profile Show & Edit
    Route::get('/admin/user/profile', [ProfileController::class, 'edit'])->name('admin.user.edit');

    // Update Profile
    Route::post('/admin/user/profile/update', [ProfileController::class, 'update'])->name('admin.user.update');

    // Update Password
    Route::post('/admin/user/profile/update-password', [ProfileController::class, 'updatePassword'])->name('admin.user.updatePassword');

    // Delete Account
    Route::delete('/admin/user/delete', [ProfileController::class, 'destroy'])->name('admin.user.delete');
});


Route::prefix('branch')->controller(BranchController::class)->group(function () {

    Route::get('branches', 'index')->name('branches.index');         // List all branches
    Route::get('branches/create', 'create')->name('branches.create'); // Show create form
    Route::post('branches', 'store')->name('branches.store');         // Store new branch
    Route::get('branches/{id}', 'show')->name('branches.show');       // Show branch details
    Route::get('branches/{id}/edit', 'edit')->name('branches.edit');  // Show edit form
    Route::put('branches/{id}', 'update')->name('branches.update');   // Update branch
    Route::delete('branches/{id}', 'destroy')->name('branches.destroy'); // Delete branch
    Route::get('branches/export/excel', [BranchController::class, 'exportExcel'])->name('branches.export.excel');
    Route::get('branches/export/pdf', [BranchController::class, 'exportPdf'])->name('branches.export.pdf');
});

Route::prefix('group')->controller(GroupController::class)->group(function () {

    Route::get('groups/', 'index')->name('groups.index');         // List all groups
    Route::get('groups/create', 'create')->name('groups.create'); // Show create form
    Route::post('groups', 'store')->name('groups.store');        // Store new group
    Route::get('groups/{group}', 'show')->name('groups.show');    // Show single group
    Route::get('groups/{group}/edit', 'edit')->name('groups.edit');  // Show edit form
    Route::put('groups/{group}', 'update')->name('groups.update');   // Update group
    Route::delete('groups/{group}', 'destroy')->name('groups.destroy'); // Delete group
    Route::get('groups/export/excel', [GroupController::class, 'exportExcel'])->name('groups.export.excel');
    Route::get('groups/export/pdf', [GroupController::class, 'exportPdf'])->name('groups.export.pdf');
});


// Route::post('/member/send-otp', [MemberController::class, 'sendOtp'])->name('members.sendOtp');
// Route::post('/member/verify-otp', [MemberController::class, 'verifyOtp'])->name('members.verifyOtp');
// Route::post('/members/resend-otp', [MemberController::class, 'resendOtp'])->name('members.resendOtp');


Route::prefix('member')->name('members.')->controller(MemberController::class)->group(function () {
    Route::get('members/', 'index')->name('index');
    Route::get('members/create', 'create')->name('create');
    Route::post('members/', 'store')->name('store');
    Route::get('members/{member}', 'show')->name('show');
    Route::get('members/{member}/edit', 'edit')->name('edit');
    Route::put('members/{member}', 'update')->name('update');
    Route::delete('members/{member}', 'destroy')->name('destroy');
    Route::get('members/export/excel', [MemberController::class, 'exportExcel'])->name('export.excel');
    Route::get('members/export/pdf', [MemberController::class, 'exportPdf'])->name('export.pdf');
});




Route::prefix('loan')->name('loans.')->controller(LoanController::class)->group(function () {

    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{loan}', 'show')->name('show');
    Route::get('/{loan}/edit', 'edit')->name('edit');
    Route::put('/{loan}', 'update')->name('update');
    Route::delete('/{loan}', 'destroy')->name('destroy');
    Route::get('loans/export/excel', [LoanController::class, 'exportExcel'])->name('export.excel');
    Route::get('loans/export/pdf', [LoanController::class, 'exportPdf'])->name('export.pdf');
});
Route::get('loan-requests/{id}', [LoanController::class, 'show_loan'])
    ->name('loan-requests.show');

Route::middleware(['auth'])->group(function () {
    Route::get('loan-requests/{id}', [LoanController::class, 'show_admin'])->name('loan-requests.show');
    Route::post('loan-requests/{id}/approve', [LoanController::class, 'approve'])->name('loan-requests.approve');
    Route::post('loan-requests/{id}/reject', [LoanController::class, 'reject'])->name('loan-requests.reject');
});



Route::resource('repayments', RepaymentController::class);
Route::get('/reports/daily', [RepaymentController::class, 'dailyReport'])->name('reports.daily');
Route::get('/reports/branch', [RepaymentController::class, 'branchReport'])->name('reports.branch');
Route::get('repayments/export/pdf', [RepaymentController::class, 'exportPdfRepayments'])->name('repayments.export.pdf');
Route::get('repayments/export/excel', [RepaymentController::class, 'exportExcelRepayments'])->name('repayments.export.excel');
Route::get('reports/daily/export/excel', [RepaymentController::class, 'exportExcelDailyReport'])->name('reports.daily.export.excel');
Route::get('reports/daily/export/pdf', [RepaymentController::class, 'exportPdfDailyReport'])->name('reports.daily.export.pdf');

Route::get('/group-members', function (Illuminate\Http\Request $request) {
    $members = \App\Models\Member::where('group_id', $request->group_id)
        ->select('id', 'name', 'member_id')
        ->orderBy('name')
        ->get();

    return response()->json($members);
});

Route::get('reports/branch/export/excel', [RepaymentController::class, 'exportExcelBranchReport'])->name('reports.branch.export.excel');
Route::get('reports/branch/export/pdf', [RepaymentController::class, 'exportPdfBranchReport'])->name('reports.branch.export.pdf');

Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::resource('expenses', ExpenseController::class);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('accounts/categories', [AccountCategoryController::class, 'index'])->name('account.categories');
    Route::post('accounts/categories', [AccountCategoryController::class, 'store'])->name('account.categories.store');
    Route::delete('accounts/categories/{id}', [AccountCategoryController::class, 'destroy'])->name('account.categories.delete');
});

Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/accounts/dashboard', [AccountDashboardController::class, 'index'])->name('accounts.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('invoices', InvoiceController::class);

// PDF download
// Route::get('invoices/{invoice}/pdf', [InvoiceController::class,'downloadPdf'])->name('invoices.pdf');
Route::get('/invoice/{id}/pdf', [LoanController::class, 'exportPdf_in'])->name('invoice.pdf');
Route::get('/invoice/{id}/excel', [LoanController::class, 'exportExcel_in'])->name('invoice.excel');

Route::get('/collection-sheet/{groupId}', [CollectionSheetController::class, 'index'])->name('collection.sheet');
Route::get('collection-sheet/{groupId}/export-pdf', [CollectionSheetController::class, 'exportPdf'])->name('collection.export.pdf');
Route::get('collection-sheet/{groupId}/export-excel', [CollectionSheetController::class, 'exportExcel'])->name('collection.export.excel');

Route::get('/loans', [LoanController::class, 'groups'])->name('loans.groups');

// Show members of group
Route::get('/loans/group/{group}', [LoanController::class, 'members'])->name('loans.members');

// Show loans of member
Route::get('/loans/member/{member}', [LoanController::class, 'memberLoans'])->name('loans.memberLoans');


Route::get('/reports', [BillingController::class, 'groups'])->name('reports.groups');
Route::get('/reports/group/{group}', [BillingController::class, 'members'])->name('reports.members');
Route::get('/reports/member/{member}', [BillingController::class, 'memberBillings'])->name('reports.dailyReport');
Route::post('/repayment/{id}/pay', [BillingController::class, 'markPaid'])->name('repayment.pay');


Route::get('/groups/{group}/billings', [BillingController::class, 'groupDailyBillings'])->name('group.billings');
Route::post('/repayment/{repayment}/pay', [BillingController::class, 'RepaymentController@pay'])->name('repayment.pay');

use App\Http\Controllers\CashBookController;

// Cashbook
Route::get('/cashbook', [CashbookController::class, 'index'])->name('cashbook.index');
Route::post('/cashbook/save', [CashbookController::class, 'storeOrUpdate'])->name('cashbook.save');


// Report
Route::get('/cashbook/report', [CashbookController::class, 'report'])->name('cashbook.report');
Route::get('/cashbook/report/pdf', [CashbookController::class, 'exportPdf'])->name('cashbook.report.pdf');
Route::get('/cashbook/report/excel', [CashbookController::class, 'exportExcel'])->name('cashbook.report.excel');


Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

require __DIR__ . '/auth.php';
