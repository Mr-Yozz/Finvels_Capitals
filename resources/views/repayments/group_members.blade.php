@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 text-primary">Group: {{ $group->name }}</h4>
    <a href="{{ route('repayments.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to Groups</a>

    <div class="table-responsive shadow-sm bg-white rounded-3 p-3">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Member</th>
                    <th>Total Loans</th>
                    <th>Total Dues (₹)</th>
                    <th>Due Count</th>
                    <th>Total Paid (₹)</th>
                    <th>Next Due Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data['member']->name }}</td>
                    <td>{{ $data['totalLoans'] }}</td>
                    <td>{{ number_format($data['totalDue'], 2) }}</td>
                    <td>{{ $data['totalDueCount'] }}</td>
                    <td>{{ number_format($data['totalPaid'], 2) }}</td>
                    <td>{{ $data['nextDueDate'] ? \Carbon\Carbon::parse($data['nextDueDate'])->format('d-M-Y') : '-' }}</td>
                    <td>
                        <a href="{{ route('repayments.index', ['member_id' => $data['member']->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection