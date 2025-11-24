@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <h4 class="fw-bold mb-4">Accounts Dashboard</h4>

    <!-- Top Metrics -->
    <div class="row g-3 mb-4">
        <!-- your metrics cards here -->
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">📈 Monthly Trend</div>
                <div class="card-body">
                    <canvas id="trendChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">💼 Expenses by Category</div>
                <div class="card-body">
                    <canvas id="categoryChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Branch Totals -->
    @if($branchTotals->count())
    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">🏦 Branch Totals</div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Branch</th>
                        <th>Total Expenses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branchTotals as $branch)
                    <tr>
                        <td>{{ $branch->name }}</td>
                        <td>₹{{ number_format($branch->expenses_sum_amount ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Hidden div for passing data to JS -->
<div id="dashboard-data"
    data-months='@json($months)'
    data-monthly-collections='@json($monthlyCollections)'
    data-monthly-expenses='@json($monthlyExpenses)'
    data-category-names='@json($categoryTotals->pluck("name"))'
    data-category-values='@json($categoryTotals->pluck("expenses_sum_amount"))'>
</div>
@endsection

@section('scripts')
@php
$manifestPath = public_path('build/manifest.json');
$manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
@endphp
@if(!empty($manifest['resources/js/account-dashboard-charts.js']))
<script type="module" src="{{ asset('build/' . $manifest['resources/js/account-dashboard-charts.js']['file']) }}"></script>
@endif
@endsection