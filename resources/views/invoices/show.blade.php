@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card p-3">
        <div class="row mb-3">
            <div class="col-md-6">
                <h4>Invoice: {{ $invoice->invoice_no }}</h4>
                <p><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M, Y') }}</p>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-outline-primary" href="{{ route('invoices.downloadPdf', $invoice) }}">Download PDF</a>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <p><strong>Member ID :</strong> {{ $invoice->loan->member->id ?? '-' }}</p>
                <p><strong>Member Name :</strong> {{ $invoice->loan->member->name ?? '-' }}</p>
                <p><strong>Phone :</strong> {{ $invoice->loan->member->phone ?? '-' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Product :</strong> {{ $invoice->loan->product_name ?? '-' }}</p>
                <p><strong>Kendra :</strong> {{ $invoice->loan->branch->name ?? '-' }}</p>
                <p><strong>Loan A/C No :</strong> {{ $invoice->loan->account_no ?? '-' }}</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <p><strong>Disbursement Date:</strong> {{ optional($invoice->loan->disbursed_at) ? \Carbon\Carbon::parse($invoice->loan->disbursed_at)->format('d M, Y') : '-' }}</p>
                <p><strong>Term:</strong> {{ $invoice->loan->tenure_months }} installments ({{ ucfirst($invoice->loan->repayment_frequency) }})</p>
            </div>
            <div class="col-md-4">
                <p><strong>Loan Amount:</strong> ₹ {{ number_format($invoice->loan_amount, 2) }}</p>
                <p><strong>Processing Fee:</strong> ₹ {{ number_format($invoice->processing_fee, 2) }}</p>
            </div>
            <div class="col-md-4">
                <p><strong>Insurance:</strong> ₹ {{ number_format($invoice->insurance_amount, 2) }}</p>
                <p><strong>Total:</strong> ₹ {{ number_format($invoice->total_amount, 2) }}</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Inst. No</th>
                        <th>Date</th>
                        <th>Principal</th>
                        <th>Interest</th>
                        <th>Total</th>
                        <th>Prin OS</th>
                        <th>KM Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->lines as $line)
                        <tr>
                            <td>{{ $line->inst_no }}</td>
                            <td>{{ \Carbon\Carbon::parse($line->due_date)->format('d M, Y') }}</td>
                            <td>{{ number_format($line->principal, 2) }}</td>
                            <td>{{ number_format($line->interest, 2) }}</td>
                            <td>{{ number_format($line->total, 2) }}</td>
                            <td>{{ number_format($line->prin_os, 2) }}</td>
                            <td style="width:140px"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <p><strong>ROI / APR:</strong> {{ $invoice->loan->interest_rate }}% (Reducing Balance)</p>
            </div>
            <div class="col-md-6 text-end">
                <p>KM Signature: ______________________</p>
            </div>
        </div>
    </div>
</div>
@endsection
