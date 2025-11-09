<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RepaymentController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     return view('admin.dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/dashboard', [DashboardController::class, 'index'])
//         ->name('admin.dashboard');
// });

Route::get('/', [AuthController::class, 'showLogin']);
Route::post('login/', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
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

Route::resource('repayments', RepaymentController::class);
Route::get('/reports/daily', [RepaymentController::class, 'dailyReport'])->name('reports.daily');
Route::get('/reports/branch', [RepaymentController::class, 'branchReport'])->name('reports.branch');
Route::get('repayments/export/pdf', [RepaymentController::class, 'exportPdfRepayments'])->name('repayments.export.pdf');
Route::get('repayments/export/excel', [RepaymentController::class, 'exportExcelRepayments'])->name('repayments.export.excel');
Route::get('reports/daily/export/excel', [RepaymentController::class, 'exportExcelDailyReport'])->name('reports.daily.export.excel');
Route::get('reports/daily/export/pdf', [RepaymentController::class, 'exportPdfDailyReport'])->name('reports.daily.export.pdf');

Route::get('reports/branch/export/excel', [RepaymentController::class, 'exportExcelBranchReport'])->name('reports.branch.export.excel');
Route::get('reports/branch/export/pdf', [RepaymentController::class, 'exportPdfBranchReport'])->name('reports.branch.export.pdf');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

require __DIR__ . '/auth.php';
