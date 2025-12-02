<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashbook;
use App\Models\Repayment;
use App\Models\Loan;
use App\Exports\CashbookExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CashbookController extends Controller
{
    // ------------------------------
    // SHOW DAILY CASHBOOK PAGE
    // ------------------------------
    public function index(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        // Today record
        $cashbook = Cashbook::whereDate('date', $date)->first();

        // Yesterday closing
        $yesterday = Carbon::parse($date)->subDay()->toDateString();
        $yCashbook = Cashbook::where('date', $yesterday)->first();

        // AUTO Opening Balance
        $openingBalance = $cashbook->opening_balance
            ?? ($yCashbook->closing_balance ?? 0);

        // AUTO Total Collection
        $autoCollection = Repayment::whereDate('paid_at', $date)->sum('paid_amount');

        // Deposit / Expenses if already saved
        $deposit = $cashbook->deposit ?? 0;
        $expenses = $cashbook->expenses ?? 0;

        // AUTO Closing Balance
        $closingBalance = $openingBalance + $autoCollection - $deposit - $expenses;

        return view('cashbook.index', compact(
            'date',
            'openingBalance',
            'autoCollection',
            'deposit',
            'expenses',
            'closingBalance'
        ));
    }



    // ------------------------------
    // STORE / UPDATE DAILY CASHBOOK
    // ------------------------------
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'deposit' => 'nullable|numeric',
            'expenses' => 'nullable|numeric'
        ]);

        $date = $request->date;

        // Yesterday cashbook
        $yesterday = Carbon::parse($date)->subDay()->toDateString();
        $yCashbook = Cashbook::whereDate('date', $yesterday)->first();

        // AUTO Opening Balance
        $openingBalance = $yCashbook->closing_balance ?? 0;

        // AUTO Total Collection
        $totalCollection = Repayment::whereDate('paid_at', $date)->sum('paid_amount');

        $deposit = $request->deposit ?? 0;
        $expenses = $request->expenses ?? 0;

        // AUTO Closing Balance
        $closingBalance = $openingBalance + $totalCollection - $deposit - $expenses;

        Cashbook::updateOrCreate(
            ['date' => $date],
            [
                'opening_balance' => $openingBalance,
                'total_collection' => $totalCollection,
                'deposit' => $deposit,
                'expenses' => $expenses,
                'closing_balance' => $closingBalance
            ]
        );

        return back()->with('success', 'Cashbook updated successfully.');
    }

    public function report(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        $cashbook = Cashbook::whereDate('date', $date)->first();

        if (!$cashbook) {
            return back()->with('error', 'No cashbook found for this date.');
        }

        // Today's loan disbursements
        $loans = Loan::with('member')
            ->whereDate('disbursed_at', $date)
            ->get();

        return view('cashbook.report', compact('cashbook', 'loans', 'date'));
    }

    public function exportPdf(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        $cashbook = Cashbook::whereDate('date', $date)->first();
        $loans = Loan::with('member')
            ->whereDate('disbursed_at', $date)
            ->get();

        $logoPath = public_path('images/finvels.jpeg');

        // Convert logo to Base64
        $logo = null;
        if (file_exists($logoPath)) {
            $logo = base64_encode(file_get_contents($logoPath));
        }

        $LogoPath = public_path('images/finvel.png');
        
        $Logo = null;
        if (file_exists($LogoPath)) {
            $Logo = base64_encode(file_get_contents($LogoPath));
        }

        $pdf = PDF::loadView('exports.cashbook_pdf', compact('cashbook', 'loans', 'date', 'logo', 'Logo'))
            ->setPaper('A4', 'portrait');

        return $pdf->download("Cashbook-{$date}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        return Excel::download(new CashbookExport($date), "Cashbook-{$date}.xlsx");
    }
}
