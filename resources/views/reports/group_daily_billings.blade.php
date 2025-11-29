@extends('layouts.app')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* ... (Your provided styles here, omitted for brevity) ... */
    .card {
        border-radius: 10px;
        background: #fff;
    }

    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    /* ... */
</style>
@endsection

@section('content')
<div class="container mt-4">

    {{-- Display Header for the Daily Billing --}}
    <h3 class="mb-4">Daily Billings for {{ $group->name }} (Due Date: {{ \Carbon\Carbon::parse($date)->format('d M Y') }})</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary text-dark">
                    <tr>
                        <th>Loan ID</th>
                        <th>Member ID</th>
                        <th>Name</th>
                        <th>Due Amount</th>
                        <th>Paid Amount</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repayments as $rep)
                    <tr>
                        {{-- Loan ID --}}
                        <td>{{ $rep->loan->id ?? '-' }}</td>

                        {{-- Member ID --}}
                        <td>{{ $rep->loan->member->member_id ?? '-' }}</td>

                        {{-- Member Name --}}
                        <td class="fw-semibold">{{ $rep->loan->member->name ?? 'N/A' }}</td>

                        {{-- Due Amount --}}
                        <td class="fw-bold text-primary">{{ number_format($rep->amount, 2) }}</td>

                        {{-- Paid Amount (Assuming you have this column in your Repayment model) --}}
                        <td class="text-success">{{ number_format($rep->paid_amount, 2) }}</td>

                        {{-- Outstanding (Calculate or assume a computed property 'outstanding' on Repayment) --}}
                        {{-- NOTE: You may need to define an accessor in Repayment model for 'outstanding' 
                             or use (amount - paid_amount) --}}
                        <td class="text-danger">
                            {{ number_format(($rep->amount - $rep->paid_amount), 2) }}
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($rep->status == 'paid')
                            <span class="badge bg-success">Paid</span>
                            @elseif($rep->status == 'partial')
                            <span class="badge bg-warning text-dark">Partial Paid</span>
                            @else
                            <span class="badge bg-danger">Due</span>
                            @endif
                        </td>

                        {{-- ACTION: Pay Form --}}
                        <td class="text-center">
                            @if($rep->status != 'paid')
                            <form action="{{ route('repayment.pay', $rep->id) }}" method="POST" class="d-flex justify-content-center">
                                @csrf
                                <input type="number"
                                    step="0.01"
                                    name="amount"
                                    required
                                    placeholder="Amount"
                                    class="form-control form-control-sm"
                                    style="width:120px;"
                                    {{-- Optional: Prefill with outstanding amount --}}
                                    value="{{ number_format($rep->amount - $rep->paid_amount, 2, '.', '') }}">

                                <button class="btn btn-success btn-sm ms-2">
                                    Pay
                                </button>
                            </form>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No repayments due on this date for members in this group.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection