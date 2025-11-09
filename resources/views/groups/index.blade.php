@extends('layouts.app')

@section('styles')
{{-- Bootstrap Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    /* ===== Table & Layout ===== */
    #groupTable {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.9rem;
    }

    #groupTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        border-bottom: 2px solid #dee2e6;
    }

    #groupTable tbody tr:hover {
        background-color: #f5f9ff;
        transition: background-color 0.3s ease;
    }

    #groupTable td,
    #groupTable th {
        padding: 0.8rem 1rem;
        vertical-align: middle;
    }

    /* ===== Buttons ===== */
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

    /* ===== Alert ===== */
    .alert {
        font-size: 0.9rem;
    }

    /* ===== Pagination ===== */
    .pagination {
        margin-top: 1rem;
    }

    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    /* ===== Responsive Design ===== */
    @media (max-width: 768px) {
        h2.text-primary {
            font-size: 1.4rem;
        }

        .table-responsive {
            padding: 0.5rem;
        }

        #groupTable th,
        #groupTable td {
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
</style>
@endsection
@section('content')
<div class="container my-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h2 class="text-primary fw-bold mb-2 mb-md-0">Groups</h2>
        <a href="{{ route('groups.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Group
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('groups.export.excel') }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </a>
        <a href="{{ route('groups.export.pdf') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </a>
    </div>

    <div class="table-responsive shadow-sm rounded-3 bg-white p-3">
        <table class="table table-hover align-middle table-bordered" id="groupTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Group Name</th>
                    <th>Branch</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $group)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $group->name }}</td>
                    <td>{{ $group->branch->name ?? '-' }}</td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('groups.show', $group->id) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-sm btn-outline-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('groups.destroy', $group->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this group?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> No groups found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $groups->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection