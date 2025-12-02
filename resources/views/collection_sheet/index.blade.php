@extends('layouts.app')
@section('styles')
<title>Collection Sheet - {{ $group->name ?? 'Group' }} - {{ $date }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .header {
        text-align: center;
        margin-bottom: 6px;
    }

    .left {
        float: left;
    }

    .right {
        float: right;
    }

    .clear {
        clear: both;
    }

    /* Image (top-left sample) */
    .sheet-image {
        margin-bottom: 10px;
        width: 120px;
        height: auto;
        float: left;
        border-radius: 100%;
        margin-right: 8px;
    }

    /* Table styling */
    table.collection {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    table.collection th,
    table.collection td {
        border: 1px solid #000;
        padding: 6px;
        vertical-align: top;
        text-align: center;
    }

    table.collection th {
        font-weight: 700;
        background: #f7f7f7;
        font-size: 12px;
    }

    .small {
        font-size: 11px;
    }

    .mono {
        font-family: monospace;
        font-size: 11px;
    }

    /* make first two columns narrower */
    .col-id {
        width: 6%;
    }

    .col-member {
        width: 18%;
        text-align: left;
        padding-left: 8px;
    }

    .col-loan-balances {
        width: 28%;
    }

    .col-dues {
        width: 28%;
    }

    .col-loans-extra {
        width: 20%;
    }

    /* loan-instance and due-instance lines */
    .inst-line {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 2px 0;
        border-bottom: 1px solid #eee;
    }

    /* footer summary box */
    .summary-box {
        width: 60%;
        margin-top: 12px;
        border: 1px solid #000;
        padding: 8px;
    }

    .sign-row {
        margin-top: 30px;
        display: flex;
        justify-content: space-between;
        width: 80%;
        margin-left: auto;
        margin-right: auto;
    }

    .sign-item {
        text-align: center;
        width: 30%;
        border-top: 1px solid #000;
        padding-top: 6px;
        font-weight: 600;
    }

    /* print optimizations */
    @media print {
        body {
            margin: 6mm;
        }

        .no-print {
            display: none;
        }
    }

    /* Date filter form styling */
    .date-filter-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
    }

    .date-filter-box form {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .date-filter-box label {
        font-weight: 600;
        color: #495057;
        margin: 0;
    }

    .date-filter-box input[type="date"] {
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
    }

    .date-filter-box button {
        padding: 6px 20px;
    }
</style>
@endsection
@section('content')
<!-- <pre>
        Branch: {{ $group->branch ? 'YES' : 'NO' }}
        User: {{ $group->branch->manager ? 'YES' : 'NO' }}
        Role: {{ $group->branch?->user?->role ?? 'NULL' }}
        Name: {{ $group->branch?->user?->name ?? 'NULL' }}
    </pre> -->

{{-- Date Filter Form --}}
<div class="date-filter-box no-print">
    <form method="GET" action="{{ route('collection.sheet', $groupId) }}">
        <label for="date_filter">Select Date:</label>
        <input
            type="date"
            id="date_filter"
            name="date"
            value="{{ $date }}"
            class="form-control"
            style="display: inline-block; width: auto;">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-calendar-check me-1"></i> View Collection Sheet
        </button>
        <a href="{{ route('collection.sheet', $groupId) }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-clockwise me-1"></i> Today
        </a>
    </form>
</div>

<div class="mb-3 d-flex gap-2">
    <a href="{{ route('collection.export.pdf', $groupId) }}?date={{ $date }}" class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
    </a>

    <a href="{{ route('collection.export.excel', $groupId) }}?date={{ $date }}" class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
    </a>
</div>
<div class="header">
    <img src="{{ $sheetImagePath }}" alt="logo" class="sheet-image">
    <h2 style="margin:0">FinVels Loans</h2>
    <h3 style="margin:0">COLLECTION SHEET</h3>
    <div style="margin-top:6px; font-size:12px;">
        <span>{{ $group->branch->name ?? 'Branch' }}</span> &nbsp; | &nbsp;
        <strong>{{ $group->name ?? '' }}</strong> &nbsp; | &nbsp;
        Date: <strong>{{ $date }}</strong> &nbsp; | &nbsp;
        Manager: {{ $group->branch->manager->name ?? 'Not Assigned' }}

    </div>
</div>

<div class="clear"></div>

<table class="collection" role="table" aria-label="Collection sheet">
    <thead>
        <tr>
            <th class="col-id" rowspan="2">ID</th>
            <th class="col-member" rowspan="2">MEMBER</th>

            <th class="col-loan-balances" colspan="2">LOAN BALANCES</th>

            <th class="col-dues" colspan="2">DUES</th>
            <th class="col-loans-extra" rowspan="2">Signature</th>

            <!-- <th class="col-loans-extra" rowspan="2">LOANS<br><span class="small">MEMB ADV | DUE DISB | SPOUSE KYC | PR | SANCHAY PRODUCT DUE | LP/PA/L</span></th> -->
        </tr>

        <tr>
            <th class="small">LOAN INSTANCE</th>
            <th class="small">TOTAL</th>

            <th class="small">Due Amount</th>
            <th class="small">Due Date</th>
        </tr>
    </thead>

    <tbody>
        @php
        // Filter rows to show only members with dues on the selected date
        $filteredRows = collect($rows)->filter(function($r) {
        return isset($r['has_due_on_date']) && $r['has_due_on_date'] && ($r['next_due_amount'] ?? 0) > 0;
        });
        @endphp

        @if($filteredRows->count() > 0)
        @foreach($filteredRows as $r)
        <tr>
            <td class="mono">{{ $r['member_id'] }}</td>
            <td class="col-member">{{ $r['member_name'] }}</td>

            <td style="text-align:left; padding-left:6px">
                {{-- show each loan instance line (multi-line) --}}
                @if(count($r['loan_instances']))
                @foreach($r['loan_instances'] as $li)
                <span class="inst-line">{{ $li }}</span>
                @endforeach
                @else
                <span class="inst-line">-</span>
                @endif
            </td>

            <td style="width:8%;">{{ number_format($r['loan_total_balance'],2) }}</td>

            <td style="text-align:left; padding-left:6px;">
                {{-- Display due amount for selected date --}}
                <span class="inst-line">₹ {{ number_format($r['next_due_amount'], 2) }}</span>
            </td>

            <td style="width:8%;">
                {{-- Display due date for selected date --}}
                @if(isset($r['next_due_date']) && $r['next_due_date'])
                {{ \Carbon\Carbon::parse($r['next_due_date'])->format('d M Y') }}
                @else
                -
                @endif
            </td>

            <td style="text-align:left; padding-left:6px;">
                <!-- <div class="small">
                    MEM ADV: {{ number_format($r['member_adv'] ?? 0,2) }}<br>
                    DUE DISB: {{ number_format($r['due_disb'] ?? 0,2) }}<br>
                    SPOUSE: {{ $r['spouse_kyc'] ?: '-' }}<br>
                    PR: {{ $r['pr'] ?? 0 }}<br>
                    SANCHAY: {{ $r['sanchay_due'] ?? 0 }}<br>
                    LP/PA/L: {{ $r['lp_pa_l'] ?? '-' }}
                </div> -->
            </td>
        </tr>
        @endforeach
        @else
        {{-- Show message if no members have dues on selected date --}}
        <tr>
            <td colspan="7" style="text-align:center; padding:20px; color: #6c757d; font-style: italic;">
                @if(\Carbon\Carbon::parse($date)->isToday())
                No dues for today
                @else
                No dues for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                @endif
            </td>
        </tr>
        @endif

        {{-- Totals Row --}}
        @if($filteredRows->count() > 0)
        <tr>
            <td colspan="3" style="text-align:right; font-weight:700;">TOTAL</td>
            <td style="font-weight:700;">
                {{ number_format($filteredRows->sum('loan_total_balance'),2) }}
            </td>
            <td style="text-align:right; font-weight:700;">TOTAL DUE</td>
            <td style="font-weight:700;">
                {{ number_format($filteredRows->sum('next_due_amount'),2) }}
            </td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>

{{-- summary box --}}
@php
// Calculate summary from filtered rows (only members with dues on selected date)
$filteredSummary = [
'due_collections' => $filteredRows->sum('next_due_amount'),
'other_collections' => $filteredRows->sum('member_adv') + $filteredRows->sum('pr') + $filteredRows->sum('sanchay_due'),
'due_disbursements' => $filteredRows->sum('due_disb'),
'other_disbursements' => 0,
];
$filteredSummary['total_collections'] = $filteredSummary['due_collections'] + $filteredSummary['other_collections'];
$filteredSummary['total_disbursements'] = $filteredSummary['due_disbursements'] + $filteredSummary['other_disbursements'];
@endphp

@if($filteredRows->count() > 0)
<div style="display:flex; gap:20px; margin-top:14px;">
    <div class="summary-box">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:60%;">Due Collections</td>
                <td style="text-align:right;">{{ number_format($filteredSummary['due_collections'],2) }}</td>
            </tr>
            <tr>
                <td>Other Collections</td>
                <td style="text-align:right;">{{ number_format($filteredSummary['other_collections'],2) }}</td>
            </tr>
            <tr>
                <td>Total Collections</td>
                <td style="text-align:right;">{{ number_format($filteredSummary['total_collections'],2) }}</td>
            </tr>
            <tr>
                <td>Due Disbursements</td>
                <td style="text-align:right;">{{ number_format($filteredSummary['due_disbursements'],2) }}</td>
            </tr>
            <tr>
                <td>Other Disbursements</td>
                <td style="text-align:right;">{{ number_format($filteredSummary['other_disbursements'],2) }}</td>
            </tr>
            <tr>
                <td>Total Disbursements</td>
                <td style="text-align:right;">{{ number_format($filteredSummary['total_disbursements'],2) }}</td>
            </tr>
        </table>
    </div>

    <div style="flex:1;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td>Applications Taken</td>
                <td style="text-align:right">{{ $summary['applications_taken'] }}</td>
            </tr>
            <tr>
                <td>No. of Loans Issued</td>
                <td style="text-align:right">{{ $summary['no_loans_issued'] }}</td>
            </tr>
            <tr>
                <td>Absentees/Defaults</td>
                <td style="text-align:right">{{ $summary['absentees_defaults'] }}</td>
            </tr>
            <tr>
                <td>Amount taken back to Office</td>
                <td style="text-align:right">{{ number_format($summary['amount_taken_back_office'],2) }}</td>
            </tr>
        </table>
    </div>
</div>
@endif

<div class="sign-row">
    <div class="sign-item">Member Leader</div>
    <div class="sign-item">Branch</div>
    <div class="sign-item">Cashier</div>
</div>

@endsection

@section('scripts')

@endsection