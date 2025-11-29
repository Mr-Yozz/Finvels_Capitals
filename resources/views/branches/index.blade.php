@extends('layouts.app')
@section('styles')
{{-- Bootstrap Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    /* ===== Table & Layout ===== */
    #branchTable {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.9rem;
    }

    #branchTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        border-bottom: 2px solid #dee2e6;
    }

    #branchTable tbody tr:hover {
        background-color: #f5f9ff;
        transition: background-color 0.3s ease;
    }

    #branchTable td,
    #branchTable th {
        padding: 0.8rem 1rem;
        vertical-align: middle;
    }

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

        #branchTable th,
        #branchTable td {
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
        <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
            <button class="btn btn-secondary btn-sm shadow-sm" onclick="history.back()">
                <i class="bi bi-arrow-left"></i> Back
            </button>
            <h2 class="text-primary fw-bold mb-0">Branches</h2>
        </div>

        <a href="{{ route('branches.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Branch
        </a>

        <form action="{{ route('branches.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search branch..."
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-search"></i> Search
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('branches.export.excel') }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </a>
        <a href="{{ route('branches.export.pdf') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </a>
    </div>

    <div class="table-responsive shadow-sm rounded-3 bg-white p-3">
        <table class="table table-hover align-middle table-bordered" id="branchTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $branch->name }}</td>
                    <td>{{ $branch->address }}</td>

                    {{-- Actions column --}}
                    <td class="text-center">
                        <div class="d-flex justify-content-between align-items-center">

                            {{-- LEFT: Always show View --}}
                            <a href="{{ route('groups.index', ['branch_id' => $branch->id]) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-plus-lg me-1"></i> Group
                            </a>

                            {{-- RIGHT: Admin-only dropdown for Edit/Delete --}}
                            @if(auth()->user()->role === 'admin')
                            <div class="btn-group">
                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('branches.show', $branch->id) }}">
                                            <i class="bi bi-eye me-2"></i>Show
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('branches.edit', $branch->id) }}">
                                            <i class="bi bi-pencil-square me-2"></i>Edit
                                        </a>
                                    </li>

                                    <li>
                                        <form action="{{ route('branches.destroy', $branch->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this branch?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> No branches found.
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $branches->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection