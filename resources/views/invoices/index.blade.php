@extends('layouts.app')

@section('content')

<h3 class="mb-3">Invoices</h3>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Invoice No</th>
            <th>Member</th>
            <th>Loan Amount</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $invoice->invoice_no }}</td>
            <td>{{ $invoice->loan->member->name ?? '-' }}</td>
            <td>{{ number_format($invoice->loan->principal, 2) }}</td>
            <td>{{ $invoice->created_at->format('d M Y') }}</td>

            <td>
                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-primary btn-sm">
                    View
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $invoices->links() }}

@endsection