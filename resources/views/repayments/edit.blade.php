@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-primary mb-3">Edit Repayment</h2>

    <form action="{{ route('repayments.update', $repayment->id) }}" method="POST" class="card p-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Loan</label>
            <select name="loan_id" class="form-select" required>
                @foreach($loans as $loan)
                    <option value="{{ $loan->id }}" {{ $repayment->loan_id == $loan->id ? 'selected' : '' }}>
                        {{ $loan->id }} - {{ $loan->member->name ?? '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" value="{{ $repayment->due_date }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" value="{{ $repayment->amount }}" class="form-control" step="0.01" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Paid Amount</label>
            <input type="number" name="paid_amount" value="{{ $repayment->paid_amount }}" class="form-control" step="0.01">
        </div>

        <div class="mb-3">
            <label class="form-label">Paid At</label>
            <input type="datetime-local" name="paid_at" value="{{ $repayment->paid_at }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="pending" {{ $repayment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="partial" {{ $repayment->status == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid" {{ $repayment->status == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Repayment</button>
    </form>
</div>
@endsection
