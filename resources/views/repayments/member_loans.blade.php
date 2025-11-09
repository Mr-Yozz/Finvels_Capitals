@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h4 class="mb-3 text-primary">💳 Loans — {{ $member->name }}</h4>
    <a href="{{ route('repayments.index', ['group_id' => $member->group_id]) }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Members
    </a>

    <div class="table-responsive shadow-sm bg-white p-3 rounded-3">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Branch</th>
                    <th>Principal</th>
                    <th>Interest Rate</th>
                    <th>Tenure (months)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($loans as $loan)
                <tr>
                    <td>#{{ $loan->id }}</td>
                    <td>{{ $loan->branch->name ?? '-' }}</td>
                    <td>₹{{ number_format($loan->principal, 2) }}</td>
                    <td>{{ $loan->interest_rate }}%</td>
                    <td>{{ $loan->tenure_months }}</td>
                    <td>
                        <span class="badge bg-{{ $loan->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($loan->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('repayments.index', ['loan_id' => $loan->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View Repayments
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No loans found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection