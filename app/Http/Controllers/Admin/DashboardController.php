<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Repayment;
use App\Models\Branch;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        // Summary cards
        $summary = [
            'total_loans' => Loan::count(),
            'active_loans' => Loan::where('status', 'active')->count(),
            'pending_loans' => Loan::where('status', 'pending')->count(),
            'closed_loans' => Loan::where('status', 'closed')->count(),
            'total_disbursed' => Loan::sum('principal'),
            'upcoming_repayments' => Repayment::whereBetween('due_date', [$today, $nextWeek])->count(),
            'overdue_repayments' => Repayment::where('due_date', '<', $today)->where('status', 'pending')->count(),
        ];

        // Total Outstanding = total principal - total repaid
        $summary['total_outstanding'] = Loan::sum('principal') - Repayment::where('status', 'paid')->sum('amount');

        // Monthly loan disbursement chart (current year)
        $monthlyDisbursed = Loan::select(
            DB::raw('MONTH(disbursed_at) as month'),
            DB::raw('SUM(principal) as total')
        )
            ->whereYear('disbursed_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Branch-wise distribution
        $branchWise = Loan::select('branch_id', DB::raw('SUM(principal) as total'))
            ->groupBy('branch_id')
            ->with('branch:id,name')
            ->get();

        // Repayment trend (due vs paid per month)
        $repaymentTrend = Repayment::select(
            DB::raw('MONTH(due_date) as month'),
            DB::raw('SUM(amount) as total_due'),
            DB::raw('SUM(CASE WHEN status="paid" THEN amount ELSE 0 END) as total_paid')
        )
            ->whereYear('due_date', date('Y'))
            ->groupBy('month')
            ->get();

        // Recent Loans
        $recentLoans = Loan::with('member', 'branch')
            ->latest('disbursed_at')
            ->take(10)
            ->get();

        // Recent Admin Actions (Audit Logs)
        $recentLogs = AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Audit Log this dashboard view
        AuditLog::log(Auth::id(), 'view_dashboard', ['page' => 'Loan Dashboard']);

        return view('admin.dashboard', compact(
            'summary',
            'monthlyDisbursed',
            'branchWise',
            'repaymentTrend',
            'recentLoans',
            'recentLogs'
        ));
        // return view('admin.dashboard', compact('totalLoans', 'totalRepayments'));
    }

    public function user(){

        return view('admin.user');
    }
}
