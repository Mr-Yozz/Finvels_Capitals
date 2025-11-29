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
    <h3>All Groups</h3>
    <table class="table">
        <tr>
            <th>Group Name</th>
            <th>Action</th>
        </tr>

        @foreach($groups as $group)
        <tr>
            <td>{{ $group->name }}</td>
            <td>
                <a href="{{ route('group.billings', $group->id) }}" class="btn btn-primary">View Members (Billings)</a>
                <!-- <a href="{{ route('reports.members', $group->id) }}" class="btn btn-primary">View Members</a> -->
            </td>
        </tr>
        @endforeach
    </table>

    {{ $groups->links() }}

</div>
@endsection

@section('scripts')

@endsection