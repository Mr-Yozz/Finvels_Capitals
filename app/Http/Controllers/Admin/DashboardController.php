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
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     $notifications = [];

    //     if (Auth::check()) {
    //         $notifications = DatabaseNotification::where('notifiable_id', Auth::id())
    //             ->where('notifiable_type', get_class(Auth::user()))
    //             ->whereNull('read_at')
    //             ->latest()
    //             ->get();
    //     }
    //     $today = Carbon::today();
    //     $nextWeek = Carbon::today()->addDays(7);

    //     // Summary cards
    //     $summary = [
    //         'total_loans' => Loan::count(),
    //         'active_loans' => Loan::where('status', 'active')->count(),
    //         'pending_loans' => Loan::where('status', 'pending')->count(),
    //         'closed_loans' => Loan::where('status', 'closed')->count(),
    //         'total_disbursed' => Loan::sum('principal'),
    //         'upcoming_repayments' => Repayment::whereBetween('due_date', [$today, $nextWeek])->count(),
    //         'overdue_repayments' => Repayment::where('due_date', '<', $today)->where('status', 'pending')->count(),
    //     ];

    //     // Total Outstanding = total principal - total repaid
    //     $summary['total_outstanding'] = Loan::sum('principal') - Repayment::where('status', 'paid')->sum('amount');

    //     // Monthly loan disbursement chart (current year)
    //     $monthlyDisbursed = Loan::select(
    //         DB::raw('MONTH(disbursed_at) as month'),
    //         DB::raw('SUM(principal) as total')
    //     )
    //         ->whereYear('disbursed_at', date('Y'))
    //         ->groupBy('month')
    //         ->pluck('total', 'month')
    //         ->toArray();

    //     // Branch-wise distribution
    //     $branchWise = Loan::select('branch_id', DB::raw('SUM(principal) as total'))
    //         ->groupBy('branch_id')
    //         ->with('branch:id,name')
    //         ->get();

    //     // Repayment trend (due vs paid per month)
    //     $repaymentTrend = Repayment::select(
    //         DB::raw('MONTH(due_date) as month'),
    //         DB::raw('SUM(amount) as total_due'),
    //         DB::raw('SUM(CASE WHEN status="paid" THEN amount ELSE 0 END) as total_paid')
    //     )
    //         ->whereYear('due_date', date('Y'))
    //         ->groupBy('month')
    //         ->get();

    //     // Recent Loans
    //     $recentLoans = Loan::with('member', 'branch')
    //         ->latest('disbursed_at')
    //         ->take(10)
    //         ->get();

    //     // Recent Admin Actions (Audit Logs)
    //     $recentLogs = AuditLog::with('user')
    //         ->latest()
    //         ->take(10)
    //         ->get();

    //     // Audit Log this dashboard view
    //     AuditLog::log(Auth::id(), 'view_dashboard', ['page' => 'Loan Dashboard']);

    //     return view('admin.dashboard', compact(
    //         'summary',
    //         'monthlyDisbursed',
    //         'branchWise',
    //         'repaymentTrend',
    //         'recentLoans',
    //         'recentLogs',
    //         'notifications'
    //     ));
    //     // return view('admin.dashboard', compact('totalLoans', 'totalRepayments'));
    // }

    public function index()
    {
        /* ---------------- NOTIFICATIONS ---------------- */
        $notifications = [];
        if (Auth::check()) {
            $notifications = DatabaseNotification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', get_class(Auth::user()))
                ->whereNull('read_at')
                ->latest()
                ->get();
        }

        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        /* ---------------- SUMMARY CARDS ---------------- */
        $totalDisbursed = Loan::sum('principal');
        $totalPaid = Repayment::where('status', 'paid')->sum('paid_amount');

        $summary = [
            'total_loans'        => Loan::count(),
            'active_loans'       => Loan::where('status', 'active')->count(),
            'pending_loans'      => Loan::where('status', 'pending')->count(),
            'closed_loans'       => Loan::where('status', 'closed')->count(),
            'total_disbursed'    => $totalDisbursed,
            'total_outstanding' => $totalDisbursed - $totalPaid,
            'upcoming_repayments' => Repayment::whereBetween('due_date', [$today, $nextWeek])
                ->where('status', '!=', 'paid')->count(),
            'overdue_repayments'  => Repayment::where('due_date', '<', $today)
                ->where('status', '!=', 'paid')->count(),
        ];

        /* ---------------- MONTHLY DISBURSEMENT CHART ---------------- */
        $monthlyDisbursed = Loan::select(
            DB::raw('MONTH(disbursed_at) as month'),
            DB::raw('SUM(principal) as total')
        )
            ->whereYear('disbursed_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyLabels = [];
        $monthlyDisbursement = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyLabels[] = Carbon::create()->month($i)->format('M');
            $monthlyDisbursement[] = $monthlyDisbursed[$i] ?? 0;
        }

        /* ---------------- BRANCH WISE DISTRIBUTION ---------------- */
        $branchData = Loan::select('branch_id', DB::raw('SUM(principal) as total'))
            ->groupBy('branch_id')
            ->with('branch:id,name')
            ->get();

        $branchDistribution = [];
        foreach ($branchData as $row) {
            $branchDistribution[$row->branch->name ?? 'Unknown'] = $row->total;
        }

        /* ---------------- REPAYMENT TREND ---------------- */
        $repaymentTrend = Repayment::select(
            DB::raw('MONTH(due_date) as month'),
            DB::raw('SUM(amount) as total_due'),
            DB::raw('SUM(CASE WHEN status = "paid" THEN paid_amount ELSE 0 END) as total_paid')
        )
            ->whereYear('due_date', date('Y'))
            ->groupBy('month')
            ->get();

        $repaymentExpected = [];
        $repaymentActual = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthRow = $repaymentTrend->firstWhere('month', $i);
            $repaymentExpected[] = $monthRow->total_due ?? 0;
            $repaymentActual[]   = $monthRow->total_paid ?? 0;
        }

        /* ---------------- RECENT DATA ---------------- */
        $recentLoans = Loan::with('member', 'branch')
            ->latest('disbursed_at')
            ->take(10)
            ->get();

        $recentLogs = AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        /* ---------------- AUDIT DASHBOARD VIEW ---------------- */
        AuditLog::log(Auth::id(), 'view_dashboard', ['page' => 'Loan Dashboard']);

        /* ---------------- SEND TO VIEW ---------------- */
        return view('admin.dashboard', compact(
            'summary',
            'monthlyLabels',
            'monthlyDisbursement',
            'branchDistribution',
            'repaymentExpected',
            'repaymentActual',
            'recentLoans',
            'recentLogs',
            'notifications'
        ));
    }

    public function user()
    {
        return view('admin.user');
    }
}
