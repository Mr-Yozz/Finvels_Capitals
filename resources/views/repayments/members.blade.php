@extends('layouts.app')
@section('styles')
@section('content')
<!-- <div class="container my-4">
    <h2 class="text-primary fw-bold mb-4">Select Member</h2>

    <div class="row">
        @forelse($members as $member)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">{{ $member->name }}</h5>
                            <p class="text-muted small mb-2">Member ID: #{{ $member->id }}</p>
                            <p class="mb-1"><i class="bi bi-telephone me-1"></i> {{ $member->mobile ?? '-' }}</p>
                            <p class="mb-0"><i class="bi bi-envelope me-1"></i> {{ $member->email ?? '-' }}</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('repayments.index', ['member_id' => $member->id]) }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-credit-card-2-front me-1"></i> View Repayments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4 text-muted">
                <i class="bi bi-people"></i> No members found.
            </div>
        @endforelse
    </div>
</div> -->

<!-- {{--<div class="container my-4">
    <h2 class="text-primary fw-bold mb-4">Member Repayment Summary</h2>

    <div class="row g-3">
        @forelse($members as $member)
            @php
                $totalLoans = $member->loans->count();
                $totalDue = 0;
                $totalPaid = 0;
                $nextDueDate = null;

                foreach ($member->loans as $loan) {
                    foreach ($loan->repayments as $r) {
                        $totalDue += $r->status !== 'paid' ? $r->amount : 0;
                        $totalPaid += $r->paid_amount ?? 0;
                        if ($r->status === 'pending' && !$nextDueDate) {
                            $nextDueDate = \Carbon\Carbon::parse($r->due_date)->format('d M Y');
                        }
                    }
                }
            @endphp

            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-semibold">{{ $member->name }}</h5>
                        <p class="mb-1"><strong>Total Loans:</strong> {{ $totalLoans }}</p>
                        <p class="mb-1"><strong>Total Due:</strong> ₹{{ number_format($totalDue, 2) }}</p>
                        <p class="mb-1"><strong>Total Paid:</strong> ₹{{ number_format($totalPaid, 2) }}</p>
                        <p class="mb-2"><strong>Next Due Date:</strong> {{ $nextDueDate ?? '-' }}</p>
                        <a href="{{ route('repayments.index', ['member_id' => $member->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View Repayments
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted text-center py-4">No members found.</p>
        {{--@endforelse--}}
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $members->links('pagination::bootstrap-5') }}
    </div>
</div>--}} -->

<div class="container my-4">
    <h2 class="text-primary fw-bold mb-4">Member Repayment Summary</h2>

    <div class="table-responsive shadow-sm bg-white rounded-3 p-3">
        <table class="table table-hover align-middle table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Total Loans</th>
                    <th>Total Dues</th>
                    <th>Total Paid</th>
                    <th>Next Due Date</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $member)
                @php
                $totalLoans = $member->loans->count();
                $totalDueAmount = 0;
                $totalPaid = 0;
                $totalDueCount = 0;
                $nextDueDate = null;

                foreach ($member->loans as $loan) {
                foreach ($loan->repayments as $r) {
                if ($r->status !== 'paid') {
                $totalDueAmount += $r->amount;
                $totalDueCount++;
                if (!$nextDueDate || \Carbon\Carbon::parse($r->due_date)->lt($nextDueDate)) {
                $nextDueDate = \Carbon\Carbon::parse($r->due_date)->format('d M Y');
                }
                }
                $totalPaid += $r->paid_amount ?? 0;
                }
                }
                @endphp

                <tr>
                    <td>{{ $members->firstItem() + $index }}</td>
                    <td class="fw-semibold text-primary">{{ $member->name }}</td>
                    <td>{{ $totalLoans }}</td>
                    <td>
                        {{ $totalDueCount }} due{{ $totalDueCount > 1 ? 's' : '' }}
                        <span class="text-muted small">(₹{{ number_format($totalDueAmount, 2) }})</span>
                    </td>
                    <td>₹{{ number_format($totalPaid, 2) }}</td>
                    <td>{{ $nextDueDate ?? '-' }}</td>
                    <td class="text-center">
                        <a href="{{ route('repayments.index', ['member_id' => $member->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View Repayments
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> No members found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $members->links('pagination::bootstrap-5') }}
    </div>
</div>


@endsection