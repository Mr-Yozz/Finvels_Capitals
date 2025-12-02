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
        <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
            <h2 class="fw-bold text-primary mb-0">
                <i class="bi bi-people-fill me-2"></i> Members
            </h2>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('members.create', ['group_id' => $group_id]) }}" class="btn btn-success mb-3">
                <i class="bi bi-plus-lg me-1"></i> Add Member
            </a>
            @endif
            <button class="btn btn-secondary btn-sm shadow-sm" onclick="history.back()">
                <i class="bi bi-arrow-left"></i> Back
            </button>
        </div>
        <div class="mb-3">
            <input type="text" id="searchMember" class="form-control" placeholder="Search members...">
        </div>
    </div>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('members.export.excel') }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
        </a>
        <a href="{{ route('members.export.pdf') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </a>
    </div>



    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary text-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Aadhaar</th>
                        <th>PAN</th>
                        <!-- <th>Group</th> -->
                        <!-- <th>Branch</th> -->
                        <th>Bank Name</th>
                        <th>Account No</th>
                        <th>IFSC Code</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    @php
                    // Normalize name for checking
                    $lowerName = strtolower($member->name);

                    // Determine role for display (fallback to name detection)
                    if ($member->role == 'leader' || str_contains($lowerName, 'leader')) {
                    $roleDisplay = 'leader';
                    } elseif ($member->role == 'sub_leader' || str_contains($lowerName, 'sub')) {
                    $roleDisplay = 'sub_leader';
                    } else {
                    $roleDisplay = 'member';
                    }
                    @endphp
                    <tr>
                        <!-- <td>{{ $loop->iteration }}</td> -->
                        <td>{{ $member->member_id }}</td>
                        <!-- <td class="fw-semibold">{{ $member->name }}</td> -->
                        <td class="fw-semibold">
                            @if($roleDisplay === 'leader')
                            <span style="font-size:20px; font-weight:bold; color:#0056D2;">
                                {{ $member->name }}
                            </span>

                            @elseif($roleDisplay === 'sub_leader')
                            <span style="font-size:17px; font-weight:600; color:#4A90E2;">
                                {{ $member->name }}
                            </span>

                            @else
                            <span style="font-size:15px; color:#000;">
                                {{ $member->name }}
                            </span>
                            @endif
                        </td>
                        <td>{{ $member->mobile }}</td>
                        <td>{{ $member->aadhaar_encrypted }}</td>
                        <td>{{ $member->pan_encrypted }}</td>
                        <!-- <td>{{ $member->group->name ?? '-' }}</td> -->
                        <!-- <td>{{ $member->group->branch->name ?? '-' }}</td> -->
                        <td>{{ $member->bank_name ?? '-' }}</td>
                        <td>{{ $member->account_number ?? '-' }}</td>
                        <td>{{ $member->ifsc_code ?? '-' }}</td>
                        <!-- <td>{{ $member->group->branch->name ?? '-' }}</td> -->
                        <td class="text-center d-flex justify-content-between align-items-center">
                            <!-- Left side: Add Loan -->
                            <a href="{{ route('loans.create', ['member_id' => $member->id]) }}"
                                class="btn btn-success btn-sm me-2">
                                Add Loan
                            </a>

                            <!-- Right side: View/Edit/Delete -->
                            <div class="d-flex gap-1">
                                <a href="{{ route('members.show', $member->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('members.edit', $member->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('members.destroy', $member->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this member?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
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

    <div class="mt-3">
        {{ $members->links() }}
    </div>

</div>

@endsection

@section('scripts')
<script>
    document.getElementById('searchMember').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        let visible = false;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const match = text.includes(searchValue);
            row.style.display = match ? '' : 'none';
            if (match) visible = true;
        });

        // Show/Hide "No members found" dynamic message
        let noDataRow = document.getElementById('noResultsRow');
        if (!visible) {
            if (!noDataRow) {
                const tableBody = document.querySelector('table tbody');
                tableBody.insertAdjacentHTML('beforeend',
                    `<tr id="noResultsRow"><td colspan="8" class="text-center py-4 text-muted">No matching records found.</td></tr>`
                );
            }
        } else if (noDataRow) {
            noDataRow.remove();
        }
    });
</script>

@endsection