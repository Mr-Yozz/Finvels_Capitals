<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use PDF;
use DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $invoices = Invoice::with('loan')->orderBy('created_at', 'desc')->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('invoices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'invoice_date' => 'nullable|date',
            'processing_fee' => 'nullable|numeric',
            'insurance_amount' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $loan = \App\Models\Loan::findOrFail($data['loan_id']);

        $invoice = Invoice::create([
            'loan_id' => $loan->id,
            'invoice_no' => 'INV-' . date('Y') . '-' . $loan->id . '-' . strtoupper(\Illuminate\Support\Str::random(4)),
            'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
            'loan_amount' => $loan->principal,
            'processing_fee' => $data['processing_fee'] ?? 0,
            'insurance_amount' => $data['insurance_amount'] ?? 0,
            'total_amount' => $loan->principal + ($data['processing_fee'] ?? 0) + ($data['insurance_amount'] ?? 0),
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
        $invoice->load('loan', 'lines', 'loan.member', 'loan.branch');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
        $invoice->load('loan', 'lines');
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
        $data = $request->validate([
            'invoice_date' => 'nullable|date',
            'processing_fee' => 'nullable|numeric',
            'insurance_amount' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $invoice->update([
            'invoice_date' => $data['invoice_date'] ?? $invoice->invoice_date,
            'processing_fee' => $data['processing_fee'] ?? $invoice->processing_fee,
            'insurance_amount' => $data['insurance_amount'] ?? $invoice->insurance_amount,
            'total_amount' => $invoice->loan_amount + ($data['processing_fee'] ?? $invoice->processing_fee) + ($data['insurance_amount'] ?? $invoice->insurance_amount),
            'notes' => $data['notes'] ?? $invoice->notes,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted');
    }
}
