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
<div class="container my-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
            <h2 class="text-primary fw-bold mb-2 mb-md-0">Groups</h2>
            <a href="{{ route('groups.create', ['branch_id' => $branch_id]) }}" class="btn btn-success shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Add Group
            </a>
            <button class="btn btn-secondary btn-sm shadow-sm" onclick="history.back()">
                <i class="bi bi-arrow-left"></i> Back
            </button>

        </div>
        <form class="d-flex gap-2">
            <input type="text" id="searchGroup" class="form-control form-control-sm" placeholder="Search groups...">
        </form>
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
                        <div class="d-flex justify-content-between align-items-center">

                            {{-- LEFT: Members button (visible to all users) --}}
                            <a href="{{ route('members.index', ['group_id' => $group->id]) }}"
                                class="btn btn-sm btn-success me-1">
                                <i class="bi bi-people-fill me-1"></i> Members
                            </a>

                            {{-- RIGHT: Admin-only Show/Edit/Delete --}}
                            @if(auth()->user()->role === 'admin')
                            <div class="btn-group">
                                <a href="{{ route('groups.show', $group->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('groups.destroy', $group->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this group?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @endif

                        </div>
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

    <!-- <div class="d-flex justify-content-center mt-3">
        {{ $groups->links('pagination::bootstrap-5') }}
    </div> -->
    @if(!request('search'))
    <div class="d-flex justify-content-center mt-3">
        {{ $groups->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('searchGroup').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#groupTable tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
</script>

@endsection