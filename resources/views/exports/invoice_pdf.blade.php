<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Invoice #{{ $invoice->invoice_no ?? '-' }} - {{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</title>

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

        /* header with two logos */
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
        }

        .meta {
            margin-top: 6px;
            font-size: 11px;
            color: #555;
        }

        /* top info table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .info-table td {
            padding: 6px 8px;
            vertical-align: top;
            font-size: 12px;
        }

        .info-table .label {
            width: 18%;
            font-weight: 700;
            background: #f6f6f6;
        }

        /* main schedule table */
        table.lines {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        /* repeat on page breaks */
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

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* totals */
        .totals {
            width: 320px;
            float: right;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px 8px;
            border: 1px solid #bbb;
            font-size: 12px;
        }

        .totals .label {
            background: #f6f6f6;
            font-weight: 700;
        }

        /* notes / footer */
        .notes {
            margin-top: 36px;
            font-size: 11px;
            color: #444;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- HEADER --}}
        <div class="header-row">
            @if(!empty($logoBase64))
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="left logo" class="logo logo-left">
            @endif

            @if(!empty($LogoBase64))
            <img src="data:image/png;base64,{{ $LogoBase64 }}" alt="right logo" class="logo logo-right">
            @endif

            <div class="title-wrap">
                <h1>Invoice</h1>
                <div class="meta">
                    Invoice #: <strong>{{ $invoice->invoice_no ?? '-' }}</strong> &nbsp; | &nbsp;
                    Date: <strong>{{ optional($invoice->created_at)->format('d M Y') ?? \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</strong>
                </div>
            </div>
        </div>

        {{-- TOP INFO --}}
        @php
        $loan = $invoice->loan ?? null;
        $member = $loan->member ?? null;
        $branch = $loan->branch ?? null;
        // totals from invoice lines
        $lines = $invoice->lines ?? collect([]);
        if (is_array($lines)) $lines = collect($lines);
        $totalPrincipal = $lines->sum(fn($l)=> (float) data_get($l,'principal',0));
        $totalInterest = $lines->sum(fn($l)=> (float) data_get($l,'interest',0));
        $totalLineTotal = $lines->sum(fn($l)=> (float) data_get($l,'total',0));
        @endphp

        <table class="info-table">
            <tr>
                <td class="label">Member ID</td>
                <td>{{ $member->member_id ?? ($member->id ?? '-') }}</td>

                <td class="label">Branch</td>
                <td>{{ $branch->name ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Member Name</td>
                <td>{{ $member->name ?? '-' }}</td>

                <td class="label">Loan Account</td>
                <td>{{ $loan->id ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Product</td>
                <td>{{ data_get($loan,'product.name', $loan->product_name ?? 'Business Loan') }}</td>

                <td class="label">Disbursed On</td>
                <td>{{ optional($loan->disbursed_at)->format('d M Y') ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Loan Amount</td>
                <td>₹ {{ number_format((float) ($loan->principal ?? $invoice->amount ?? 0), 2) }}</td>

                <td class="label">Tenure</td>
                <td>{{ $loan->tenure_months ?? ($loan->tenure ?? '-') }} months</td>
            </tr>

            <tr>
                <td class="label">ROI / APR</td>
                <td>{{ $loan->interest_rate ?? '-' }}% @if(!empty($loan->apr)) / {{ $loan->apr }}% @endif</td>

                <td class="label">Processing Fee</td>
                <td>₹ {{ number_format((float) ($loan->processing_fee ?? 0), 2) }}</td>
            </tr>
        </table>

        {{-- LINES / SCHEDULE --}}
        <h3 style="margin-top:16px; margin-bottom:6px;">Repayment Schedule</h3>

        <table class="lines">
            <thead>
                <tr>
                    <th style="width:8%;" class="text-center">Inst. No</th>
                    <th style="width:18%;">Due Date</th>
                    <th style="width:18%;" class="text-right">Principal (₹)</th>
                    <th style="width:18%;" class="text-right">Interest (₹)</th>
                    <th style="width:18%;" class="text-right">Total (₹)</th>
                    <th style="width:20%;">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($lines as $line)
                <tr>
                    <td class="text-center">{{ data_get($line,'inst_no','-') }}</td>
                    <td>{{ optional(data_get($line,'due_date'))->format('d M Y') ?? (data_get($line,'due_date') ? \Carbon\Carbon::parse(data_get($line,'due_date'))->format('d M Y') : '-') }}</td>
                    <td class="text-right"> {{ number_format((float) data_get($line,'principal',0), 2) }}</td>
                    <td class="text-right"> {{ number_format((float) data_get($line,'interest',0), 2) }}</td>
                    <td class="text-right"> {{ number_format((float) data_get($line,'total', (data_get($line,'principal',0) + data_get($line,'interest',0)) ), 2) }}</td>
                    <td>{{ data_get($line,'status','Pending') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No schedule lines available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- totals --}}
        <div class="clearfix">
            <table class="totals" align="right">
                <tr>
                    <td class="label">Total Principal</td>
                    <td class="text-right">₹ {{ number_format($totalPrincipal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Total Interest</td>
                    <td class="text-right">₹ {{ number_format($totalInterest, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Grand Total</td>
                    <td class="text-right">₹ {{ number_format($totalLineTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- notes / footer --}}
        <div class="notes">
            <strong>Notes:</strong>
            <p style="margin:6px 0 0; line-height:1.4;">
                {{ $invoice->notes ?? 'Please pay by the due date. For enquiries contact support.' }}
            </p>

            <p style="margin-top:18px; font-size:11px; color:#666;">
                Generated by: {{ $generatedBy ?? auth()->user()->name ?? 'System' }} |
                Generated on: {{ now()->format('d M Y, h:i A') }}
            </p>
        </div>
    </div>
</body>

</html>