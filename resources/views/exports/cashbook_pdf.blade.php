<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Daily Report - {{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</title>

    <style>
        /* page settings for PDF engines (dompdf/snappy) */
        @page {
            margin: 18mm 12mm;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 12px;
            color: #111;
            margin: 0;
        }

        /* header with two logos and centered title */
        .header-row {
            width: 100%;
            position: relative;
            min-height: 90px;
            /* reserve space for logos */
            margin-bottom: 6px;
        }

        .logo {
            height: 78px;
            width: auto;
            object-fit: contain;
            border-radius: 50%;
            display: block;
        }

        .logo-left {
            position: absolute;
            left: 0;
            top: 0;
        }

        .logo-right {
            position: absolute;
            right: 0;
            top: 0;
        }

        .title-wrap {
            text-align: center;
            padding-top: 6px;
            /* align below logos */
        }

        .title-wrap h1 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 0.4px;
        }

        .title-wrap .meta {
            margin-top: 4px;
            font-size: 11px;
            color: #555;
        }

        /* table base styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        /* repeat on page break */
        tfoot {
            display: table-footer-group;
        }

        th,
        td {
            border: 1px solid #bbb;
            padding: 6px 8px;
            vertical-align: middle;
            font-size: 12px;
        }

        th {
            background: #f6f6f6;
            font-weight: 700;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* small, clean denomination table */
        .denom td,
        .denom th {
            text-align: center;
        }

        /* zebra rows for readability */
        tbody tr:nth-child(even) {
            background: #fbfbfb;
        }

        /* small-screen fallbacks (for browser preview) */
        @media (max-width:600px) {
            .logo {
                height: 56px;
            }

            th,
            td {
                font-size: 11px;
                padding: 5px;
            }

            .title-wrap h1 {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header-row">
        @if(!empty($logoBase64))
        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="left logo" class="logo logo-left">
        @endif

        @if(!empty($LogoBase64))
        <img src="data:image/png;base64,{{ $LogoBase64 }}" alt="right logo" class="logo logo-right">
        @endif

        <div class="title-wrap">
            <h1>Daily Report</h1>
            <div class="meta">
                {{ $organizationName ?? ($group->branch->name ?? '') }} &nbsp;|&nbsp;
                Date: {{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}
            </div>
        </div>
    </div>

    {{-- SUMMARY TABLE --}}
    <table>
        <tbody>
            <tr>
                <td style="width:50%;">Opening Balance</td>
                <td class="text-right" style="width:50%;">{{ number_format((float)($cashbook->opening_balance ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td>Total Collection</td>
                <td class="text-right">{{ number_format((float)($cashbook->total_collection ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td>Deposit</td>
                <td class="text-right">{{ number_format((float)($cashbook->deposit ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td>Expenses</td>
                <td class="text-right">{{ number_format((float)($cashbook->expenses ?? 0), 2) }}</td>
            </tr>
            <tr>
                <th>Closing Balance</th>
                <th class="text-right">{{ number_format((float)($cashbook->closing_balance ?? 0), 2) }}</th>
            </tr>
        </tbody>
    </table>

    {{-- LOANS DISBURSED TODAY --}}
    <h3 style="margin-top:18px; margin-bottom:6px;">Today's Loan Disbursement</h3>

    @php
    // ensure $loans is a Collection for safe methods
    if (!isset($loans)) { $loans = collect([]); }
    elseif (is_array($loans)) { $loans = collect($loans); }

    $totalPrincipal = $loans->sum(function($loan){
    return (float) ($loan->principal ?? 0);
    });
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width:15%;" class="text-center">Loan ID</th>
                <th style="width:55%;">Member Name</th>
                <th style="width:30%;" class="text-right">Loan Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
            <tr>
                <td class="text-center">{{ $loan->id ?? '-' }}</td>
                <td>{{ optional($loan->member)->name ?? '-' }}</td>
                <td class="text-right">{{ number_format((float)($loan->principal ?? 0), 2) }}</td>
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
                <th colspan="2" class="text-right">Total Principal (₹)</th>
                <th class="text-right">{{ number_format($totalPrincipal, 2) }}</th>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- DENOMINATION TABLE --}}
    <h3 style="margin-top:18px; margin-bottom:6px;">Denomination Table</h3>

    <table class="denom" style="max-width:420px;">
        <thead>
            <tr>
                <th>Note</th>
                <th>Count</th>
                <th>Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            @php
            // prepare denom keys (controller can pass actual counts in $denominations as associative array)
            $denomKeys = ['2000','500','200','100','50','20','10','Coins'];
            $denominations = $denominations ?? [];
            @endphp

            @foreach($denomKeys as $d)
            <tr>
                <td class="text-center">{{ $d }}{{ $d !== 'Coins' ? ' X' : '' }}</td>
                <td class="text-center">{{ isset($denominations[$d]) ? number_format($denominations[$d]) : '' }}</td>
                <td class="text-right">{{ isset($denominations[$d]) ? number_format((float)$denominations[$d] * ($d === 'Coins' ? 1 : (int)$d), 2) : '' }}</td>
            </tr>
            @endforeach

            {{-- optional total of denominations if values provided --}}
            @php
            $denomTotal = 0;
            foreach($denominations as $k=>$v){
            if ($k === 'Coins') { $denomTotal += (float)$v; }
            elseif (is_numeric($k)) { $denomTotal += (float)$v * (float)$k; }
            }
            @endphp

            <tr>
                <th class="text-right">Denomination Total</th>
                <th class="text-center"></th>
                <th class="text-right">{{ $denomTotal > 0 ? number_format($denomTotal, 2) : '' }}</th>
            </tr>
        </tbody>
    </table>

</body>

</html>