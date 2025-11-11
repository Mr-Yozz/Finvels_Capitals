@extends('layouts.app')
@section('styles')
<style>
    .icon-wrapper {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .hover-shadow:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12) !important;
        transition: all 0.3s ease-in-out;
    }
</style>
@endsection
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">📊 Loan Dashboard</h4>
        <a href="{{ route('accounts.dashboard') }}" class="btn btn-primary btn-lg">
            Account Dashboard
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        @foreach([
        ['Total Loans', $summary['total_loans'], 'bi bi-collection', 'images/loan.jpg', 'bg-gradient-primary', $summaryTrends['total_loans'] ?? []],
        ['Active Loans', $summary['active_loans'], 'bi bi-check-circle', 'images/activeIcon.png', 'bg-gradient-success', $summaryTrends['active_loans'] ?? []],
        ['Pending Loans', $summary['pending_loans'], 'bi bi-hourglass-split', 'images/pending.png', 'bg-gradient-warning', $summaryTrends['pending_loans'] ?? []],
        ['Closed Loans', $summary['closed_loans'], 'bi bi-lock', 'images/close.png', 'bg-gradient-secondary', $summaryTrends['closed_loans'] ?? []],
        ['Total Disbursed', '₹'.number_format($summary['total_disbursed']), 'bi bi-cash-stack', 'images/distribution.png', 'bg-gradient-info', $summaryTrends['total_disbursed'] ?? []],
        ['Total Outstanding', '₹'.number_format($summary['total_outstanding']), 'bi bi-graph-down', 'images/outstanding.png', 'bg-gradient-danger', $summaryTrends['total_outstanding'] ?? []],
        ['Upcoming Repayments', $summary['upcoming_repayments'], 'bi bi-bell', 'images/upcoming.png', 'bg-gradient-success', $summaryTrends['upcoming_repayments'] ?? []],
        ['Overdue Repayments', $summary['overdue_repayments'], 'bi bi-exclamation-triangle', 'images/over.png', 'bg-gradient-danger', $summaryTrends['overdue_repayments'] ?? []],
        ] as $card)
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100 hover-shadow">
                <div class="card-body d-flex align-items-center">
                    <!-- Gradient Box -->
                    <div class="d-flex align-items-center justify-content-center rounded {{ $card[4] }}"
                        style="width: 50px; height: 50px;">

                        <img src="{{ asset($card[3]) }}" alt="{{ $card[0] }}" style="width:50px; height:50px;"
                            onerror="this.style.display='none'; this.parentNode.querySelector('i').style.display='block';">
                        <i class="{{ $card[2] }} fs-2 text-white" style="display:none;"></i>
                    </div>

                    <div class="w-100 ms-3">
                        <h6 class="text-muted mb-1">{{ $card[0] }}</h6>
                        <h5 class="fw-bold">
                            <span class="counter" data-count="{{ is_numeric($card[1]) ? $card[1] : preg_replace('/[^0-9]/','',$card[1]) }}">
                                0
                            </span>
                        </h5>
                        <canvas class="sparkline mt-2 w-100"
                            data-values='@json($card[5])' height="30"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts -->
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <div class="card p-3 shadow-sm">
                <h6 class="fw-bold">Monthly Loan Disbursement</h6>
                <canvas id="loanChart"
                    data-labels='@json($monthlyLabels ?? [])'
                    data-values='@json($monthlyDisbursement ?? [])'></canvas>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card p-3 shadow-sm">
                <h6 class="fw-bold">Branch-wise Loan Distribution</h6>
                <canvas id="branchChart"
                    data-labels='@json(array_keys($branchDistribution ?? []))'
                    data-values='@json(array_values($branchDistribution ?? []))'></canvas>
            </div>
        </div>
    </div>

    <!-- Repayment Trend -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card p-3 shadow-sm">
                <h6 class="fw-bold">Repayment Performance Trend</h6>
                <canvas id="repaymentChart"
                    data-labels='@json($monthlyLabels ?? [])'
                    data-expected='@json($repaymentExpected ?? [])'
                    data-actual='@json($repaymentActual ?? [])'></canvas>
            </div>
        </div>
    </div>
</div>

@endsection



@section('scripts')
@vite(['resources/js/dashboard-charts.js'])
@endsection