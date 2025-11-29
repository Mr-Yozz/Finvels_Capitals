@extends('layouts.app')
@section('styles')
<style>
    /* ===== Loan Details Page Custom CSS ===== */

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-top: 15px;
    }

    .field-row {
        display: flex;
        font-size: 14px;
        padding: 4px 0;
    }

    .field-row .label {
        width: 135px;
        font-weight: 600;
        color: #003d91;
    }

    .field-row .value {
        font-weight: 500;
        color: #333;
    }


    .small-fields {
        display: flex;
        gap: 20px;
        margin: 25px 0;
    }

    .small-box {
        background: #f1f6ff;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 14px;
        border: 1px solid #e2e8f0;
    }

    .small-box .label {
        font-weight: 600;
        color: #003d91;
    }

    .small-box .value {
        font-weight: 500;
        color: #333;
    }


    /* Tables */
    .tables-wrap {
        display: flex;
        margin-top: 25px;
    }

    .amort-box {
        width: 100%;
        background: #fff;
        padding: 0;
        border-radius: 12px;
        border: 1px solid #e7e7e7;
        overflow-x: auto;
    }

    table.amort {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .amort thead {
        background: #0d6efd;
        color: #fff;
        white-space: nowrap;
    }

    .amort th,
    .amort td {
        padding: 6px 10px;
        text-align: center;
        border: 1px solid #e1e7f5;
    }

    .amort tbody tr:nth-child(even) {
        background: #f7f9ff;
    }

    .amort tbody tr:hover {
        background: #eaf2ff;
    }


    /* Responsive (Mobile support) */
    @media(max-width: 992px) {
        .detail-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .small-fields {
            flex-direction: column;
        }

        .field-row .label {
            width: 130px;
        }
    }
</style>
@endsection

@section('content')
<h2 class="text-primary mb-3">Loan Details</h2>

<div class="card p-4">
    <p><strong class="text-primary">ID:</strong> {{ $loan->id }}</p>
    <p><strong class="text-primary">Member:</strong> {{ $loan->member->name ?? '-' }}</p>
    <p><strong class="text-primary">Branch:</strong> {{ $loan->branch->name ?? '-' }}</p>
    <p><strong class="text-primary">Principal:</strong> {{ number_format($loan->principal,2) }}</p>
    <p><strong class="text-primary">Interest Rate:</strong> {{ $loan->interest_rate }}%</p>
    <p><strong class="text-primary">Tenure:</strong> {{ $loan->tenure_months }} months</p>
    @if($loan->repayment_frequency === 'weekly')
    <p><strong class="text-primary">EMI:</strong> ₹{{ number_format($loan->weekly_emi, 2) }}</p>
    @else
    <p><strong class="text-primary">EMI:</strong> ₹{{ number_format($loan->monthly_emi, 2) }}</p>
    @endif
    <p><strong class="text-primary">Disbursed At:</strong> {{ $loan->disbursed_at }}</p>
    <p><strong class="text-primary">Status:</strong> {{ ucfirst($loan->status) }}</p>

    <h4 class="text-primary mt-4">Invoice</h4>
    <!-- {{-- <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loan->repayments as $repayment)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $repayment->due_date }}</td>
                <td>{{ number_format($repayment->amount,2) }}</td>
                <td>{{ ucfirst($repayment->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table> --}} -->
    @if($loan->invoice)
    <a href="{{ route('invoice.pdf', $loan->invoice->id) }}" class="btn btn-danger">
        Download PDF
    </a>

    <a href="{{ route('invoice.excel', $loan->invoice->id) }}" class="btn btn-success">
        Download Excel
    </a>
    @endif
    <!-- <div style="text-align:left; margin-bottom:20px; border-radius:100%">
        <img src="{{ asset('images/finvels.jpeg') }}" alt="Company Logo" style="height:80px;">
    </div> -->
    <div style="text-align:left; margin-bottom:20px;">
        <img src="{{ asset('images/finvels.jpeg') }}"
            alt="Company Logo"
            style="height:80px; width:80px; border-radius:50%; object-fit:cover;">
    </div>
    <!-- Borrower / Loan core details -->
    <div class="detail-grid">
        <div>
            <div class="field-row">
                <div class="label">Member ID :</div>
                <div class="value">{{ $loan->member->member_id ?? '-' }}</div>
            </div>
            <div class="field-row">
                <div class="label">Branch Name :</div>
                <div class="value">{{ $loan->branch->name ?? '-' }}</div>
            </div>
            <div class="field-row">
                <div class="label">Product Name :</div>
                <div class="value">{{ $loan->product_name ?? 'Loan Product' }}</div>
            </div>
            <div class="field-row">
                <div class="label">Loan Purpose :</div>
                <div class="value">{{ $loan->purpose ?? '-' }}</div>
            </div>
        </div>

        <div>
            <div class="field-row">
                <div class="label">Member Name :</div>
                <div class="value">{{ $loan->member->name ?? '-' }}</div>
            </div>
            <div class="field-row">
                <div class="label">Loan Acc Num :</div>
                <div class="value">{{ $loan->id }}</div>
            </div>
            <div class="field-row">
                <div class="label">Moratorium :</div>
                <div class="value">{{ $loan->moratorium ?? '-' }}</div>
            </div>
            <div class="field-row">
                <div class="label">Disbursement Date :</div>
                <div class="value">{{ $loan->disbursed_at?->format('d M Y') }}</div>
            </div>
        </div>

        <div>
            <div class="field-row">
                <div class="label">Spouse Name:</div>
                <div class="value">{{ $loan->spousename ?? '-' }}</div>
            </div>
            <div class="field-row">
                <div class="label">Loan Amount:</div>
                <div class="value">₹ {{ number_format($loan->principal,2) }}</div>
            </div>
            <div class="field-row">
                <div class="label">ROI / APR :</div>
                <div class="value">{{ $loan->interest_rate }}% / {{ number_format($loan->interest_rate + 5,2) }}%</div>
            </div>
            <div class="field-row">
                <div class="label">Term Of Loan:</div>
                <div class="value">{{ $loan->tenure_months }} ({{ ucfirst($loan->repayment_frequency) }})</div>
            </div>
        </div>
    </div>

    <!-- Fees / phone line -->
    <div class="small-fields">
        <div class="small-box">
            <div class="label">Processing Fee (PF) :</div>
            <div class="value">₹ {{ number_format($loan->processing_fee ?? 0,2) }}</div>
        </div>
        <div class="small-box">
            <div class="label">Insurance Premium :</div>
            <div class="value">₹ {{ number_format($loan->insurance_amount ?? 0,2) }}</div>
        </div>
        <div class="small-box">
            <div class="label">Phone :</div>
            <div class="value">{{ $loan->member->mobile ?? '-' }}</div>
        </div>
    </div>

    <!-- Amortization schedule -->
    <div class="tables-wrap">
        <div class="amort-box">
            <table class="amort">
                <thead>
                    <tr>
                        <th>Inst. No.</th>
                        <th>Date</th>
                        <th>Principal</th>
                        <th>Interest</th>
                        <th>Total</th>
                        <th>Prin OS</th>
                        <th>KM Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loan->invoice->lines as $line)
                    <tr>
                        <td>{{ $line->inst_no }}</td>
                        <td>{{ \Carbon\Carbon::parse($line->due_date)->format('d M Y') }}</td>
                        <td>{{ number_format($line->principal,2) }}</td>
                        <td>{{ number_format($line->interest,2) }}</td>
                        <td>{{ number_format($line->total,2) }}</td>
                        <td>{{ number_format($line->prin_os,2) }}</td>
                        <td>{{ $line->km_signature ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('loans.index') }}" class="btn btn-secondary mt-3">Back</a>
    <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-primary mt-3">Edit</a>

</div>

@endsection