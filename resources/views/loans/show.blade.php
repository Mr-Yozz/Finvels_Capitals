@extends('layouts.app')

@section('content')

<div class="container mt-4">

    {{-- =====================  CARD 1 : LOAN REQUEST DETAILS  ===================== --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Loan Request Details</h5>
        </div>

        <div class="card-body">

            {{-- Flash --}}
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

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
                        <td>₹ {{ number_format($loanRequest->principal, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Interest Rate</th>
                        <td>{{ $loanRequest->interest_rate }}%</td>
                    </tr>
                    <tr>
                        <th>Tenure</th>
                        <td>{{ $loanRequest->tenure_months }} months</td>
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

            {{-- ACTIONS --}}
            @if($loanRequest->is_approved == 'pending')
            <div class="mt-3">
                <form action="{{ route('loan-requests.approve', $loanRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success">Approve</button>
                </form>

                <form action="{{ route('loan-requests.reject', $loanRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-danger">Reject</button>
                </form>
            </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('loan-requests.index') }}" class="btn btn-secondary">Back</a>
            </div>

        </div>
    </div>



    {{-- =====================  CARD 2 : INVOICE + LOAN DETAILS + EMI TABLE  ===================== --}}
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Loan Invoice & EMI Schedule</h5>
        </div>

        <div class="card-body">

            {{-- Company Logo --}}
            <div class="mb-3">
                <img src="{{ asset('images/finvels.jpeg') }}"
                    style="height:80px;width:80px;border-radius:50%;object-fit:cover;">
            </div>

            {{-- TOP SUMMARY DETAILS --}}
            <div class="row mb-3">

                <div class="col-md-4">
                    <p><strong>Member:</strong> {{ $loanRequest->member->name }}</p>
                    <p><strong>Branch:</strong> {{ $loanRequest->branch->name }}</p>
                    <p><strong>Loan Purpose:</strong> {{ $loanRequest->purpose }}</p>
                </div>

                <div class="col-md-4">
                    <p><strong>Principal:</strong> ₹ {{ number_format($loanRequest->principal,2) }}</p>
                    <p><strong>Interest Rate:</strong> {{ $loanRequest->interest_rate }}%</p>
                    <p><strong>Tenure:</strong> {{ $loanRequest->tenure_months }} months</p>
                </div>

                <div class="col-md-4">
                    <p><strong>Status:</strong> {{ ucfirst($loanRequest->is_approved) }}</p>
                    <p><strong>Product:</strong> {{ $loanRequest->product_name }}</p>
                </div>

            </div>


            {{-- DOWNLOAD BUTTONS --}}
            @if($loanRequest->invoice)
            <a href="{{ route('invoice.pdf', $loanRequest->invoice->id) }}" class="btn btn-danger mb-3">
                Download PDF
            </a>

            <a href="{{ route('invoice.excel', $loanRequest->invoice->id) }}" class="btn btn-success mb-3">
                Download Excel
            </a>
            @endif



            {{-- EMI TABLE --}}
            @if($loanRequest->invoice && $loanRequest->invoice->lines->count())
            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Inst. No</th>
                            <th>Due Date</th>
                            <th>Principal</th>
                            <th>Interest</th>
                            <th>Total</th>
                            <th>Closing Balance</th>
                            <th>Signature</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loanRequest->invoice->lines as $line)
                        <tr>
                            <td>{{ $line->inst_no }}</td>
                            <td>{{ \Carbon\Carbon::parse($line->due_date)->format('d M Y') }}</td>
                            <td>{{ number_format($line->principal,2) }}</td>
                            <td>{{ number_format($line->interest,2) }}</td>
                            <td>{{ number_format($line->total,2) }}</td>
                            <td>{{ number_format($line->prin_os,2) }}</td>
                            <td>{{ $line->km_signature ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>

</div>

@endsection