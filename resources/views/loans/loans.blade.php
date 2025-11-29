@extends('layouts.app')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .card {
        border-radius: 10px;
        background: #fff;
    }

    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th {
        background-color: #e9f3ff !important;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
        padding: 12px;
    }

    .table td {
        vertical-align: middle;
        padding: 12px;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f9ff !important;
        transition: 0.2s ease-in-out;
    }

    .btn {
        border-radius: 8px;
        font-size: 13px;
        transition: all 0.2s ease-in-out;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .badge {
        font-size: 0.75rem;
        padding: 6px 10px;
        border-radius: 6px;
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .table thead th {
        border-bottom: 2px solid #dee2e6;
    }

    .table td,
    .table th {
        border-color: #dee2e6 !important;
    }
</style>
@endsection
@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">
            <i class="bi bi-cash-coin me-2"></i> Loans
        </h2>
        <a href="{{ route('loans.create', ['member_id' => $member->id]) }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Loan
        </a>

        <div class="mb-3">
            <input type="text" id="searchLoan" class="form-control" placeholder="Search loans...">
        </div>
    </div>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('loans.export.excel') }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </a>
        <a href="{{ route('loans.export.pdf') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary text-dark">
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Branch</th>
                        <th>Principal</th>
                        <th>Interest Rate</th>
                        <th>Tenure (Months)</th>
                        <th>EMI</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $loan->member->name ?? '-' }}</td>
                        <td>{{ $loan->branch->name ?? '-' }}</td>
                        <td>₹{{ number_format($loan->principal, 2) }}</td>
                        <td>{{ $loan->interest_rate }}%</td>
                        <td>{{ $loan->tenure_months }}</td>
                        @if($loan->repayment_frequency === 'weekly')
                        <td>₹{{ number_format($loan->weekly_emi, 2) }}</td>
                        @else
                        <td>₹{{ number_format($loan->monthly_emi, 2) }}</td>
                        @endif
                        <td>
                            @if($loan->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                            @elseif($loan->status == 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($loan->status == 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                            @else
                            <span class="badge bg-secondary">{{ ucfirst($loan->status) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-outline-secondary btn-sm me-1" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-outline-primary btn-sm me-1" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('loans.destroy', $loan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this loan?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No loans found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $loans->links() }}
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.getElementById('searchLoan').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');
        let visible = false;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const match = text.includes(searchValue);
            row.style.display = match ? '' : 'none';
            if (match) visible = true;
        });

        // No records message
        let noResultsRow = document.getElementById('noResultsRow');
        if (!visible) {
            if (!noResultsRow) {
                const tbody = document.querySelector('table tbody');
                tbody.insertAdjacentHTML('beforeend',
                    `<tr id="noResultsRow"><td colspan="9" class="text-center py-3 text-muted">No matching loans found.</td></tr>`
                );
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    });
</script>
@endsection