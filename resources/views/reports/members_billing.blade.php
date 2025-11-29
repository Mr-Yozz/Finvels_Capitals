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
    <h3>Billing for Member #{{ $memberId }} ({{ $date }})</h3>

    <div class="card mt-3 shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0">
                <thead class="table-primary text-dark">
                    <tr>
                        <th>Loan ID</th>
                        <th>Member</th>
                        <th>Due Amount</th>
                        <th>Paid Amount</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($repayments as $rep)
                    <tr>

                        <!-- Loan ID -->
                        <td>{{ $rep->loan->id }}</td>

                        <!-- Member Name -->
                        <td>{{ $rep->loan->member->name }}</td>

                        <!-- Due Amount -->
                        <td class="fw-bold text-primary">{{ number_format($rep->amount, 2) }}</td>

                        <!-- Paid Amount -->
                        <td class="text-success">
                            {{ number_format($rep->paid_amount, 2) }}
                        </td>

                        <!-- Outstanding Amount -->
                        <td class="text-danger">
                            {{ number_format($rep->outstanding, 2) }}
                        </td>

                        <!-- Status -->
                        <td>
                            @if($rep->status == 'paid')
                            <span class="badge bg-success">Paid</span>
                            @elseif($rep->status == 'partial')
                            <span class="badge bg-warning text-dark">Partial Paid</span>
                            @else
                            <span class="badge bg-danger">Due</span>
                            @endif
                        </td>

                        <!-- ACTION: Enter Payment -->
                        <td>
                            @if($rep->status != 'paid')
                            <form action="{{ route('repayment.pay', $rep->id) }}" method="POST" class="d-flex">
                                @csrf
                                <input type="number"
                                    step="0.01"
                                    name="amount"
                                    required
                                    placeholder="Amount"
                                    class="form-control form-control-sm"
                                    style="width:120px;">

                                <button class="btn btn-success btn-sm ms-2">
                                    Pay
                                </button>
                            </form>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>

                    </tr>
                    @endforeach

                    @if($repayments->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">No Records</td>
                    </tr>
                    @endif
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection