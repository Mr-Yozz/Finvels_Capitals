@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="mb-3">Daily Cashbook</h3>

    <!-- Date Form -->
    <form method="GET" action="{{ route('cashbook.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="date" class="form-control" name="date" value="{{ $date }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Load</button>
        </div>
    </form>

    <!-- Cashbook Form -->
    <form method="POST" action="{{ route('cashbook.save') }}">
        @csrf

        <input type="hidden" name="date" value="{{ $date }}">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Opening Balance</label>
                        <input type="number" class="form-control bg-light" value="{{ $openingBalance }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label>Total Collection</label>
                        <input type="number" class="form-control bg-light" value="{{ $autoCollection }}" readonly>

                        <input type="hidden" name="total_collection" value="{{ $autoCollection }}">
                    </div>

                    <!-- <div class="col-md-4">
                        <label>Total Collection (Manual)</label>
                        <input type="number" step="0.01" name="total_collection" class="form-control"
                            value="{{ $cashbook->total_collection ?? $autoCollection }}" required>
                    </div> -->
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Deposit</label>
                        <input type="number" step="0.01" name="deposit" class="form-control"
                            value="{{ $cashbook->deposit ?? 0 }}">
                    </div>

                    <div class="col-md-4">
                        <label>Expenses</label>
                        <input type="number" step="0.01" name="expenses" class="form-control"
                            value="{{ $cashbook->expenses ?? 0 }}">
                    </div>

                    <div class="col-md-4">
                        <label>Closing Balance</label>
                        <input type="number" step="0.01" class="form-control bg-light"
                            value="{{ $cashbook->closing_balance ?? '0.00' }}" readonly>
                    </div>
                </div>

                <button class="btn btn-success mt-3">Save Cashbook</button>

            </div>
        </div>

    </form>
    <a href="{{ route('cashbook.report', ['date' => $date]) }}" class="btn btn-primary mt-3">
        View Report
    </a>
</div>
@endsection