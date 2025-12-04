<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Loans Report - {{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</title>

    <style>
        @page {
            margin: 18mm 12mm;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 12px;
            color: #111;
            margin: 0;
        }

        .container {
            padding: 12px;
        }

        /* header with corner logos and centered title */
        .header-row {
            width: 100%;
            position: relative;
            min-height: 90px;
            margin-bottom: 10px;
        }

        .logo {
            height: 72px;
            width: auto;
            object-fit: contain;
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
        }

        .title-wrap h1 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 0.4px;
        }

        .meta {
            margin-top: 6px;
            font-size: 11px;
            color: #555;
        }

        /* table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        /* repeat on PDF page breaks */
        tfoot {
            display: table-footer-group;
        }

        th,
        td {
            border: 1px solid #bbb;
            padding: 8px 10px;
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

        tbody tr:nth-child(even) {
            background: #fcfcfc;
        }

        /* totals box */
        .totals-row {
            margin-top: 10px;
            width: 100%;
        }

        .totals-row td {
            padding: 6px 8px;
        }

        @media (max-width:600px) {
            .logo {
                height: 56px;
            }

            th,
            td {
                padding: 6px;
                font-size: 11px;
            }

            .title-wrap h1 {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        @php
        // ensure loans is a collection
        if (!isset($loans)) { $loans = collect([]); }
        elseif (is_array($loans)) { $loans = collect($loans); }

        // compute totals
        $totalPrincipal = $loans->sum(function($l){ return (float) data_get($l,'principal',0); });
        $totalEMI = $loans->sum(function($l){ return (float) data_get($l,'monthly_emi',0); });
        $totalCount = $loans->count();
        @endphp

        {{-- HEADER --}}
        <div class="header-row">
            @if(!empty($logoBase64))
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="left logo" class="logo logo-left">
            @endif

            @if(!empty($LogoBase64))
            <img src="data:image/png;base64,{{ $LogoBase64 }}" alt="right logo" class="logo logo-right">
            @endif

            <div class="title-wrap">
                <h1>Loans Report</h1>
                <div class="meta">
                    Total Loans: <strong>{{ number_format($totalCount) }}</strong> &nbsp;|&nbsp;
                    Date: <strong>{{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</strong>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <table>
            <thead>
                <tr>
                    <th style="width:4%;" class="text-center">#</th>
                    <th style="width:20%;">Member</th>
                    <th style="width:18%;">Branch</th>
                    <th style="width:12%;" class="text-right">Principal (₹)</th>
                    <th style="width:10%;">Interest Rate</th>
                    <th style="width:10%;">Tenure (Mo)</th>
                    <th style="width:12%;" class="text-right">EMI (₹)</th>
                    <th style="width:14%;">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($loans as $key => $loan)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ data_get($loan, 'member.name', '-') }}</td>
                    <td>{{ data_get($loan, 'branch.name', '-') }}</td>
                    <td class="text-right"> {{ number_format((float) data_get($loan,'principal',0), 2) }}</td>
                    <td>{{ number_format((float) data_get($loan,'interest_rate',0), 2) }}%</td>
                    <td class="text-center">{{ data_get($loan,'tenure_months', data_get($loan,'tenure','-')) }}</td>
                    <td class="text-right"> {{ number_format((float) data_get($loan,'monthly_emi',0), 2) }}</td>
                    <td>{{ ucfirst(str_replace('_',' ', data_get($loan,'status','-'))) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No loans found.</td>
                </tr>
                @endforelse
            </tbody>

            @if($loans->count() > 0)
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Totals</th>
                    <th class="text-right"> {{ number_format($totalPrincipal, 2) }}</th>
                    <th></th>
                    <th class="text-center"></th>
                    <th class="text-right"> {{ number_format($totalEMI, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
            @endif
        </table>

    </div>
</body>

</html>