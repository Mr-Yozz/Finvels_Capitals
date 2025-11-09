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
    <table class="table table-bordered">
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
    </table>

    <a href="{{ route('loans.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-primary">Edit</a>
</div>
@endsection
