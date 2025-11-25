@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h4 class="mb-3">Search Result — Members</h4>
    <a href="{{ route('repayments.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <div class="table-responsive bg-white shadow-sm rounded p-3">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>#ID</th>
                    <th>Member</th>
                    <th>Loans</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                <tr>
                    <td>{{ $member->id }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->loans_count }}</td>
                    <td>
                        <a href="{{ route('repayments.index', ['member_id' => $member->id]) }}" class="btn btn-sm btn-outline-primary">
                            View Loans
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $members->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection