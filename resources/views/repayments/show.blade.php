@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-primary mb-3">Repayment Details</h2>

    <div class="card p-4">
        <p><strong>ID:</strong> {{ $repayment->id }}</p>
        <p><strong>Loan:</strong> {{ $repayment->loan->id ?? '-' }}</p>
        <p><strong>Due Date:</strong> {{ $repayment->due_date }}</p>
        <p><strong>Amount:</strong> {{ $repayment->amount }}</p>
        <p><strong>Paid Amount:</strong> {{ $repayment->paid_amount }}</p>
        <p><strong>Status:</strong> {{ ucfirst($repayment->status) }}</p>
        <p><strong>Paid At:</strong> {{ $repayment->paid_at }}</p>
        <a href="{{ route('repayments.index') }}" class="btn btn-secondary mt-3">Back</a>
    </div>
</div>
@endsection