@extends('layouts.app')
@section('styles')
{{-- Bootstrap Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
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

    .text-success {
        font-weight: 500;
    }

    .text-danger {
        font-weight: 500;
    }

    @media (max-width: 768px) {

        .table th,
        .table td {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
    }
</style>
@endsection
@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h4 class="mb-0 fw-bold text-primary">
            <i class="bi bi-building me-2"></i>Branch-Wise Report
        </h4>
        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('reports.branch.export.excel') }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </a>
        <a href="{{ route('reports.branch.export.pdf') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </a>
    </div>

    {{-- Report Table --}}
    <div class="card shadow-sm rounded-3">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary text-dark">
                        <tr>
                            <th>#</th>
                            <th>Branch</th>
                            <th>Total Loans</th>
                            <th class="text-end">Total Due (₹)</th>
                            <th class="text-end">Total Paid (₹)</th>
                            <th class="text-end">Outstanding (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reportData as $key => $branch)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td class="fw-semibold">{{ $branch['branch_name'] }}</td>
                            <td>{{ $branch['total_loans'] }}</td>
                            <td class="text-end">₹{{ number_format($branch['total_due'], 2) }}</td>
                            <td class="text-end text-success">₹{{ number_format($branch['total_paid'], 2) }}</td>
                            <td class="text-end text-danger">₹{{ number_format($branch['outstanding'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox me-1"></i>No data found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection