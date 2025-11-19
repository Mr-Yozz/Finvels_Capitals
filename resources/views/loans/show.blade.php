@extends('layouts.app')

@section('content')
<h2 class="text-primary mb-3">Loan Details</h2>

<div class="card p-4">
    <p><strong class="text-primary">ID:</strong> {{ $loan->id }}</p>
    <p><strong class="text-primary">Member:</strong> {{ $loan->member->name ?? '-' }}</p>
    <p><strong class="text-primary">Branch:</strong> {{ $loan->branch->name ?? '-' }}</p>
    <p><strong class="text-primary">Principal:</strong> {{ number_format($loan->principal,2) }}</p>
    <p><strong class="text-primary">Interest Rate:</strong> {{ $loan->interest_rate }}%</p>
    <p><strong class="text-primary">Tenure:</strong> {{ $loan->tenure_months }} months</p>
    <p><strong class="text-primary">EMI:</strong> {{ number_format($loan->monthly_emi,2) }}</p>
    <p><strong class="text-primary">Disbursed At:</strong> {{ $loan->disbursed_at }}</p>
    <p><strong class="text-primary">Status:</strong> {{ ucfirst($loan->status) }}</p>

    <h4 class="text-primary mt-4">Repayment Schedule</h4>
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
    <table class="table table-bordered text-center align-middle" style="font-size: 13px;">
        <thead>
            <tr>
                <th rowspan="2">ID</th>
                <th rowspan="2">MEMBER</th>

                <!-- LOAN BALANCES -->
                <th colspan="2">LOAN BALANCES</th>

                <!-- DUES -->
                <th colspan="2">DUES</th>

                <th rowspan="2">MEMBER ADV</th>

                <!-- LOANS -->
                <th colspan="4">LOANS</th>
            </tr>

            <tr>
                <th>LOAN INSTANCE</th>
                <th>TOTAL</th>

                <th>LOAN INSTANCE</th>
                <th>TOTAL</th>

                <th>DUE DISB</th>
                <th>SPOUSE KYC</th>
                <th>PR</th>
                <th>SANCHAY PRODUCT DUE</th>

                {{-- last column --}}
                <th>LP/P/A/L</th>
            </tr>
        </thead>

        <tbody>
            @foreach($loan->repayments as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $loan->member->name }}</td>

                <!-- LOAN BALANCE values -->
                <td>{{ $row->loan_instance }}</td>
                <td>{{ number_format($row->balance,2) }}</td>

                <!-- DUES -->
                <td>{{ $row->due_instance }}</td>
                <td>{{ number_format($row->due_total,2) }}</td>

                <!-- Member ADV -->
                <td>{{ $row->member_adv ?? 0 }}</td>

                <!-- LOANS -->
                <td>{{ $row->due_disb ?? '-' }}</td>
                <td>{{ $row->spouse_kyc ?? '-' }}</td>
                <td>{{ $row->pr ?? '-' }}</td>
                <td>{{ $row->sanchay_due ?? '-' }}</td>

                <td>{{ $row->lp_pal ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>


    <a href="{{ route('loans.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-primary">Edit</a>
</div>
@endsection