@extends('layouts.app')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* -------- Container -------- */
    .container {
        max-width: 100%;
        padding: 15px;
    }

    /* -------- Table Styling -------- */
    #repaymentTable {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.9rem;
    }

    #repaymentTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        border-bottom: 2px solid #dee2e6;
    }

    #repaymentTable tbody tr:hover {
        background-color: #f5f9ff;
        transition: background-color 0.3s ease;
    }

    #repaymentTable td,
    #repaymentTable th {
        padding: 0.8rem 1rem;
        vertical-align: middle;
    }

    /* -------- Buttons -------- */
    .btn-sm {
        font-size: 0.8rem;
        border-radius: 6px;
    }

    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: #fff;
    }

    .btn-outline-warning:hover {
        background-color: #ffc107;
        color: #000;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: #fff;
    }

    /* -------- Badges -------- */
    .badge {
        font-size: 0.8rem;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }

    /* -------- Responsive Design -------- */
    @media (max-width: 768px) {
        h2.text-primary {
            font-size: 1.4rem;
        }

        .table-responsive {
            padding: 0.5rem;
        }

        #repaymentTable th,
        #repaymentTable td {
            font-size: 0.85rem;
            padding: 0.5rem;
        }

        td.text-center.text-nowrap {
            white-space: normal !important;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .btn i {
            margin: 0;
        }
    }

    /* -------- Pagination -------- */
    .pagination {
        margin-top: 1rem;
    }

    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>
@endsection
@section('content')
<div class="container my-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h2 class="text-primary fw-bold mb-2 mb-md-0"></h2>
        <a href="{{ route('repayments.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Repayment
        </a>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h2 class="text-primary fw-bold mb-2 mb-md-0">
            Repayments — {{ $member->name }}
        </h2>
        <a href="{{ route('repayments.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Members
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('repayments.export.excel') }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </a>
        <a href="{{ route('repayments.export.pdf') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </a>
    </div>

    <div class="table-responsive mt-3 shadow-sm rounded-3 bg-white p-3">
        <table class="table table-hover align-middle table-bordered" id="repaymentTable">
            <thead class="table-light text-nowrap">
                <tr>
                    <th>ID</th>
                    <th>Loan</th>
                    <th>Due Date</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Status</th>
                    <th>Paid At</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($repayments as $index => $repayment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>#{{ $repayment->loan->id ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($repayment->due_date)->format('d M Y') }}</td>
                    <td>₹{{ number_format($repayment->amount, 2) }}</td>
                    <td>₹{{ number_format($repayment->paid_amount, 2) }}</td>
                    <td>
                        @switch($repayment->status)
                        @case('paid')
                        <span class="badge bg-success px-3 py-2">Paid</span>
                        @break
                        @case('pending')
                        <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                        @break
                        @case('overdue')
                        <span class="badge bg-danger px-3 py-2">Overdue</span>
                        @break
                        @default
                        <span class="badge bg-secondary px-3 py-2">{{ ucfirst($repayment->status) }}</span>
                        @endswitch
                    </td>
                    <td>{{ $repayment->paid_at ? \Carbon\Carbon::parse($repayment->paid_at)->format('d M Y, h:i A') : '-' }}</td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('repayments.show', $repayment->id) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('repayments.edit', $repayment->id) }}" class="btn btn-sm btn-outline-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('repayments.destroy', $repayment->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Delete this repayment?')" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> No repayments found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $repayments->appends(['member_id' => $member->id])->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection