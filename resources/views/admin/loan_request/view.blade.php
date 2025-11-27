@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h4 class="mb-3">Loan Request Details</h4>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            {{-- LOAN DETAILS TABLE --}}
            <table class="table table-bordered">
                <tbody>

                    <tr>
                        <th>ID</th>
                        <td>{{ $loanRequest->id }}</td>
                    </tr>

                    <tr>
                        <th>Member</th>
                        <td>{{ $loanRequest->member->name ?? 'N/A' }} (ID: {{ $loanRequest->member_id }})</td>
                    </tr>

                    <tr>
                        <th>Branch</th>
                        <td>{{ $loanRequest->branch->name ?? 'N/A' }}</td>
                    </tr>

                    <tr>
                        <th>Product</th>
                        <td>{{ $loanRequest->product_name }}</td>
                    </tr>

                    <tr>
                        <th>Purpose</th>
                        <td>{{ $loanRequest->purpose }}</td>
                    </tr>

                    <tr>
                        <th>Principal</th>
                        <td>{{ number_format($loanRequest->principal, 2) }}</td>
                    </tr>

                    <tr>
                        <th>Interest Rate</th>
                        <td>{{ $loanRequest->interest_rate }}%</td>
                    </tr>

                    <tr>
                        <th>Tenure (Months)</th>
                        <td>{{ $loanRequest->tenure_months }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            @if($loanRequest->is_approved == 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($loanRequest->is_approved == 'approved')
                            <span class="badge bg-success">Approved</span>
                            @else
                            <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                    </tr>

                </tbody>
            </table>

            {{-- BUTTONS (ONLY IF PENDING) --}}
            @if($loanRequest->is_approved == 'pending')
            <div class="mt-3">

                {{-- APPROVE --}}
                <form action="{{ route('loan-requests.approve', $loanRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success">Approve</button>
                </form>

                {{-- REJECT --}}
                <form action="{{ route('loan-requests.reject', $loanRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-danger">Reject</button>
                </form>

            </div>
            @endif

            {{-- BACK --}}
            <div class="mt-3">
                <button type="button" class="btn btn-secondary" onclick="history.back()">Back</button>
            </div>

        </div>
    </div>
</div>

@endsection