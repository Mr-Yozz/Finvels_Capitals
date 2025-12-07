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
            font-family: Arial, sans-serif;
            margin: 0;
            /* background-color: #f4f4f4; */
            font-size: 11px;
        }

        .report-container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* HEADER & DETAILS */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .report-header h1 {
            font-size: 18px;
            margin: 0;
        }

        .header-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        /* TABLE STYLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }

        /* Adjust numerical alignment for Loan Distribution table */
        .loan-distribution-table td:nth-child(1) {
            text-align: center;
        }

        .loan-distribution-table td:nth-child(3) {
            text-align: right;
        }

        .loan-distribution-table th:nth-child(3) {
            text-align: right;
        }

        .loan-distribution-table tfoot th:nth-child(2) {
            text-align: right;
        }

        .loan-distribution-table tfoot th:nth-child(3) {
            text-align: right;
        }

        /* MAIN SECTIONS LAYOUT */
        .main-data-section {
            display: block;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        .table-container {
            width: 100%;
        }

        .table-container.collections {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .table-container h2 {
            font-size: 13px;
            background-color: #e0e0e0;
            padding: 4px;
            margin-top: 0;
            text-align: center;
        }

        /* BOTTOM SECTIONS LAYOUT */
        .bottom-section {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            align-items: flex-start;
        }

        .loan-distribution-container {
            flex: 2;
        }

        .denomination-container {
            flex: 1;
            min-width: 300px;
        }

        .loan-distribution-container h2 {
            font-size: 13px;
            text-align: center;
            padding: 4px 0;
        }

        .denomination-container .separator {
            background-color: #fff !important;
            border: none !important;
            height: 1px;
            padding: 0;
        }

        /* FOOTER / SIGNATURES */
        .report-footer {
            display: flex;
            justify-content: space-around;
            padding-top: 20px;
            border-top: 1px dashed #ccc;
            font-size: 10px;
        }

        .signature {
            text-align: center;
            border-top: 1px solid #000;
            width: 25%;
            padding-top: 5px;
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

        /* A4 Print Specific Styles (Media Query) */
        @page {
            size: A4;
            margin: 1cm;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background-color: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-size: 10pt;
            }

            .report-container {
                width: 21cm;
                min-height: 29.7cm;
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
                page-break-after: auto;
            }

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
                /* Using a visible placeholder for demonstration */
                background-color: #f0f0f0;
                border: 2px solid #ccc;
                text-align: center;
                line-height: 74px;
                font-size: 8px;
                color: #555;
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

            h1,
            h2,
            th {
                color: #000 !important;
            }

            .main-data-section,
            .bottom-section {
                break-inside: avoid;
            }

            th,
            td {
                padding: 3px 4px;
                font-size: 9pt;
            }

            .report-header h1 {
                font-size: 16pt;
            }

            .table-container h2,
            .loan-distribution-container h2,
            .denomination-container h2 {
                font-size: 12pt;
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
    <div class="main-data-section">
        <div class="table-container collections">
            <table>
                <thead>
                    <tr>
                        <th>Opening Balance</th>
                        <th>Total Collection (Inflows)</th>
                        <th>Deposit (Outflows)</th>
                        <th>Expenses.</th>
                        <th>Closing Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ number_format((float)($cashbook->opening_balance ?? 0), 2) }}</td>
                        <td>{{ number_format((float)($cashbook->total_collection ?? 0), 2) }}</td>
                        <td>{{ number_format((float)($cashbook->deposit ?? 0), 2) }}</td>
                        <td>{{ number_format((float)($cashbook->expenses ?? 0), 2) }}</td>
                        <th>{{ number_format((float)($cashbook->closing_balance ?? 0), 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- LOANS DISBURSED TODAY --}}


    @php
    // ensure $loans is a Collection for safe methods
    if (!isset($loans)) { $loans = collect([]); }
    elseif (is_array($loans)) { $loans = collect($loans); }

    $totalPrincipal = $loans->sum(function($loan){
    return (float) ($loan->principal ?? 0);
    });
    @endphp

    <div class="bottom-section">
        <div class="loan-distribution-container">
            <h3 style="margin-top:18px; margin-bottom:6px;">Today's Loan Disbursement</h3>

            <div class="loan-distribution-table">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 15%" class="text-center">
                                Loan ID
                            </th>
                            <th style="width: 15%">Member Name</th>
                            <th style="width: 15%" class="text-right">
                                Loan Amount (₹)
                            </th>
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
                        <tr style="font-weight: bold">
                            <th colspan="2" class="text-right">
                                Total Principal (₹)
                            </th>
                            <th class="text-right">{{ number_format($totalPrincipal, 2) }}</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            <div class="denomination-container">
                <h2>Denomination</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Denomination</th>
                            <th>No.</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2000</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: right"></td>
                        </tr>
                        <tr>
                            <td>500</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: right"></td>
                        </tr>
                        <tr>
                            <td>200</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: right"></td>
                        </tr>
                        <tr>
                            <td>100</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: right"></td>
                        </tr>
                        <tr>
                            <td>50</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: right"></td>
                        </tr>
                        <tr>
                            <td>20</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: right"></td>
                            </td>
                        </tr>
                        <tr>
                            <td>10</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: right"></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: right"></td>
                        </tr>
                        <tr>
                            <td>Coins</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: right"></td>
                        </tr>

                        <tr style="font-weight: bold">
                            <td>Total</td>
                            <td></td>
                            <td style="text-align: right"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>


        <div style="margin-top: 40px; width: 100%; text-align:center;">
            <table style="width: 90%; margin: 0 auto; border-collapse: collapse;">
                <tr>
                    <td style="width:33%; padding: 20px 10px;">
                        <div style="border-top:1px solid #000; width:100%; margin:0 auto; padding-top:6px; font-weight:600;">
                            Cashier
                        </div>
                    </td>

                    <td style="width:33%; padding: 20px 10px;">
                        <div style="border-top:1px solid #000; width:100%; margin:0 auto; padding-top:6px; font-weight:600;">
                            Accountant
                        </div>
                    </td>

                    <td style="width:33%; padding: 20px 10px;">
                        <div style="border-top:1px solid #000; width:100%; margin:0 auto; padding-top:6px; font-weight:600;">
                            Branch Manager
                        </div>
                    </td>
                </tr>
            </table>
        </div>

</body>

</html>