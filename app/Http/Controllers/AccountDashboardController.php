<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repayment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Branch;
use Carbon\Carbon;

class AccountDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // 💰 Total inflows (Collections)
        $totalCollections = Repayment::sum('amount');

        // 💸 Total outflows (Expenses)
        $totalExpenses = Expense::sum('amount');

        // 📊 Net balance
        $netBalance = $totalCollections - $totalExpenses;

        // 📅 Today’s summary
        $todayCollections = Repayment::whereDate('paid_at', $today)->sum('amount');
        $todayExpenses = Expense::whereDate('expense_date', $today)->sum('amount');

        // 📦 Category-wise expense totals
        $categoryTotals = ExpenseCategory::withSum('expenses', 'amount')->get();

        // 🏦 Optional branch-wise totals
        $branchTotals = Branch::select('id', 'name')
            ->withSum('expenses', 'amount')
            ->get();

        // 📈 Monthly trend for charts (collections vs expenses)
        $months = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('M'));
        $monthlyCollections = [];
        $monthlyExpenses = [];

        foreach (range(1, 12) as $m) {
            $monthlyCollections[] = Repayment::whereMonth('paid_at', $m)->sum('amount');
            $monthlyExpenses[] = Expense::whereMonth('expense_date', $m)->sum('amount');
        }

        return view('accounts.dashboard', compact(
            'totalCollections',
            'totalExpenses',
            'netBalance',
            'todayCollections',
            'todayExpenses',
            'categoryTotals',
            'branchTotals',
            'months',
            'monthlyCollections',
            'monthlyExpenses'
        ));
    }
}
