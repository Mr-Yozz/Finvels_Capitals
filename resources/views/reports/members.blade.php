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
    <h3>Members of {{ $group->name }}</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary text-dark">
                    <tr>
                        <th>Member ID</th>
                        <th>Loan ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Branch</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                        <!-- <td>{{ $loop->iteration }}</td> -->
                        <td>{{ $member->member_id }}</td>
                        <td>{{ $member->latestLoan->id ?? '-' }}</td>
                        <td class="fw-semibold">{{ $member->name }}</td>
                        <td>{{ $member->mobile }}</td>
                        <td>{{ $member->group->branch->name ?? '-' }}</td>
                        <td class="text-center">

                            <div class="d-flex justify-content-between align-items-center">

                                {{-- LEFT: View Loans (visible for all users) --}}
                                <a href="{{ route('reports.dailyReport', $member->id) }}"
                                    class="btn btn-primary btn-sm">
                                    View Billings
                                </a>
                            </div>

                        </td>


                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No members found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{ $members->links() }}


</div>
@endsection

@section('scripts')

@endsection