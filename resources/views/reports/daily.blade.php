@extends('layouts.app')
@section('styles')
{{-- Bootstrap Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    /* Card & Table styling */
    .card {
        border-radius: 12px;
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 0.75rem 1rem;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f9ff !important;
        transition: background-color 0.2s;
    }

    .badge {
        font-size: 0.75rem;
        padding: 6px 10px;
        border-radius: 6px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {

        .table th,
        .table td {
            font-size: 0.8rem;
            padding: 0.5rem;
        }

        .d-flex.gap-2 {
            flex-direction: column !important;
            gap: 0.5rem !important;
        }
    }
</style>
@endsection
@section('content')
<div class="container mt-4">

    {{-- Header & Filters --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h4 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-event me-2"></i>Daily Billing Report</h4>

        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" action="{{ route('reports.daily') }}" class="d-flex gap-2">
                <input type="date" name="date" value="{{ $date }}" class="form-control" required>
                <button class="btn btn-primary">Filter</button>
            </form>

            <a href="{{ route('reports.branch') }}" class="btn btn-secondary">Branch Report</a>
        </div>
    </div>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('reports.daily.export.excel', ['date' => $date]) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </a>
        <a href="{{ route('reports.daily.export.pdf', ['date' => $date]) }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </a>
    </div>

    {{-- Table Card --}}
    <div class="card shadow-sm rounded-3">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary text-dark">
                        <tr>
                            <th>#</th>
                            <th>Member</th>
                            <th>Loan ID</th>
                            <th>Branch</th>
                            <th>Due Date</th>
                            <th class="text-end">Due Amount</th>
                            <th class="text-end">Paid Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($repayments as $key => $repayment)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $repayment->loan->member->name ?? 'N/A' }}</td>
                            <td>#{{ $repayment->loan->id }}</td>
                            <td>{{ $repayment->loan->branch->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($repayment->due_date)->format('d M Y') }}</td>
                            <td class="text-end">₹{{ number_format($repayment->amount, 2) }}</td>
                            <td class="text-end text-success">₹{{ number_format($repayment->paid_amount, 2) }}</td>
                            <td>
                                @if($repayment->status == 'paid')
                                <span class="badge bg-success">Paid</span>
                                @elseif($repayment->status == 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                                @else
                                <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox me-1"></i> No records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Summary Section --}}
            <div class="mt-4 border-top pt-2">
                <h6 class="fw-semibold mb-2">Summary:</h6>
                <p class="mb-0">
                    <strong>Total Due:</strong> ₹{{ number_format($totalDue, 2) }} |
                    <strong>Total Paid:</strong> ₹{{ number_format($totalPaid, 2) }} |
                    <strong>Outstanding:</strong> ₹{{ number_format($outstanding, 2) }}
                </p>
            </div>
        </div>
    </div>
</div>


@endsection