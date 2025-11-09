@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-primary mb-3">Add Repayment</h2>

    <form action="{{ route('repayments.store') }}" method="POST" class="card p-4">
        @csrf

        <div class="mb-3">
            <label class="form-label">Loan</label>
            <select name="loan_id" class="form-select" required>
                <option value="">Select Loan</option>
                @foreach($loans as $loan)
                    <option value="{{ $loan->id }}">{{ $loan->id }} - {{ $loan->member->name ?? '' }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" required step="0.01">
        </div>

        <div class="mb-3">
            <label class="form-label">Paid Amount</label>
            <input type="number" name="paid_amount" class="form-control" step="0.01">
        </div>

        <div class="mb-3">
            <label class="form-label">Paid At</label>
            <input type="datetime-local" name="paid_at" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="pending">Pending</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Repayment</button>
    </form>
</div>
@endsection
