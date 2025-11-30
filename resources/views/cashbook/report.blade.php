@extends('layouts.app')

@section('content')
<div class="container">

    <div class="text-center mb-4">
        <img src="{{ asset('images/finvels.jpg') }}" height="80">
        <h3 class="mt-2">Daily Cashbook Report</h3>
        <p>Date: <strong>{{ $date }}</strong></p>
    </div>

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
            @forelse($loans as $loan)
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
            @foreach([2000,500,200,100,50,20,10,5,2,1] as $den)
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