@extends('layouts.app')

@section('content')
<div class="container">

    <div class="text-center mb-4">
        <img src="{{ asset('images/finvels.jpg') }}" height="80">
        <h3 class="mt-2">Daily Cashbook Report</h3>
        <p>Date: <strong>{{ $date }}</strong></p>
    </div>

    <!-- <div class="text-center mb-4">
        <img src="{{ asset('images/fin.jpeg') }}" height="80">
        <h3 class="mt-2">Daily Cashbook Report</h3>
        <p>Date: <strong>{{ $date }}</strong></p>
    </div> -->

    <table class="table table-bordered">
        <tr>
            <th>Opening Balance</th>
            <td>{{ number_format($cashbook->opening_balance, 2) }}</td>
        </tr>
        <tr>
            <th>Total Collection</th>
            <td>{{ number_format($cashbook->total_collection, 2) }}</td>
        </tr>
        <tr>
            <th>Deposit</th>
            <td>{{ number_format($cashbook->deposit, 2) }}</td>
        </tr>
        <tr>
            <th>Expenses</th>
            <td>{{ number_format($cashbook->expenses, 2) }}</td>
        </tr>
        <tr class="table-primary">
            <th>Closing Balance</th>
            <td>{{ number_format($cashbook->closing_balance, 2) }}</td>
        </tr>
    </table>

    <h4 class="mt-5">Loan Disbursements</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Loan ID</th>
                <th>Member Name</th>
                <th>Loan Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalPrincipal = 0;
            @endphp

            @forelse($loans as $loan)
            @php
            $totalPrincipal += $loan->principal;
            @endphp
            <tr>
                <td>{{ $loan->id }}</td>
                <td>{{ $loan->member->name }}</td>
                <td>{{ number_format($loan->principal, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">No Loans Distributed Today</td>
            </tr>
            @endforelse
        </tbody>

        @if($loans->count() > 0)
        <tfoot>
            <tr>
                <th colspan="2" class="text-end">Total Principal</th>
                <th>{{ number_format($totalPrincipal, 2) }}</th>
            </tr>
        </tfoot>
        @endif
    </table>


    <h4 class="mt-5">Denomination Table</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Denomination</th>
                <th>Count</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['2000 X ','500 X','200 X','100 X','50 X','20 X','10 X','5 X','2 X','1 X'] as $den)
            <tr>
                <td>{{ $den }}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        <a href="{{ route('cashbook.report.pdf', ['date' => $date]) }}" class="btn btn-danger">Download PDF</a>
        <a href="{{ route('cashbook.report.excel', ['date' => $date]) }}" class="btn btn-success">Download Excel</a>
    </div>

</div>
@endsection