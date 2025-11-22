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

    <!-- repayments table -->
    <div class="table-responsive bg-white shadow-sm p-3 rounded">

        <table class="table table-bordered text-center align-middle" style="font-size: 13px;">
            <thead class="table-light">
                <tr>
                    <th rowspan="2">ID</th>
                    <th rowspan="2">MEMBER</th>

                    <th colspan="2">LOAN BALANCES</th>
                    <th colspan="2">DUES</th>

                    <th rowspan="2">MEMBER ADV</th>

                    <th colspan="4">LOANS</th>
                    <th rowspan="2">LP/P/A/L</th>
                </tr>

                <tr>
                    <th>LOAN INSTANCE</th>
                    <th>TOTAL</th>

                    <th>LOAN INSTANCE</th>
                    <th>TOTAL</th>

                    <th>DUE DISB</th>
                    <th>SPOUSE KYC</th>
                    <th>PR</th>
                    <th>SANCHAY PRODUCT DUE</th>
                </tr>
            </thead>

            <tbody>
                @foreach($group->members as $member)
                @php
                $loanInstances = [];
                $loanBalancesTotal = 0;

                $dueInstances = [];
                $dueTotal = 0;

                foreach($member->loans as $loan) {
                foreach($loan->repayments as $rep) {
                $loanInstances[] = $rep->loan_instance;
                $loanBalancesTotal += $rep->balance;

                $dueInstances[] = $rep->due_instance;
                $dueTotal += $rep->due_total;
                }
                }
                @endphp

                <tr>
                    <td>{{ $member->id }}</td>
                    <td>{{ $member->name }}</td>

                    {{-- LOAN BALANCES --}}
                    <td class="text-start">{!! nl2br(e(implode("\n", $loanInstances))) !!}</td>
                    <td>{{ number_format($loanBalancesTotal, 2) }}</td>

                    {{-- DUES --}}
                    <td class="text-start">{!! nl2br(e(implode("\n", $dueInstances))) !!}</td>
                    <td>{{ number_format($dueTotal, 2) }}</td>

                    {{-- MEMBER ADV --}}
                    <td>{{ $member->advance_amount ?? '-' }}</td>

                    {{-- LOANS --}}
                    <td>{{ $member->loans->sum('due_disb') ?? 0 }}</td>
                    <td>{{ $member->spouse_kyc ?? '-' }}</td>
                    <td>{{ $member->pr ?? '-' }}</td>
                    <td>{{ $member->sanchay_product_due ?? '-' }}</td>

                    {{-- LAST COLUMN --}}
                    <td>{{ $member->lp_pal ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>
</div>
@endsection