<!-- resources/views/exports/repayments_report.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Repayments Report - {{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</title>

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

        /* Header with corner logos and centered title */
        .header-row {
            width: 100%;
            position: relative;
            min-height: 90px;
            /* reserve space for logos */
            margin-bottom: 12px;
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

        .title-wrap .meta {
            margin-top: 6px;
            font-size: 11px;
            color: #555;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        /* repeat header on page breaks */
        tfoot {
            display: table-footer-group;
        }

        th,
        td {
            border: 1px solid #ccc;
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

        /* Badges */
        .badge-paid {
            background: #28a745;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
        }

        .badge-pending {
            background: #ffc107;
            color: #111;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
        }

        .badge-overdue {
            background: #dc3545;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
        }

        tbody tr:nth-child(even) {
            background: #fbfbfb;
        }

        .muted {
            color: #666;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="container">

        @php
        // make safe collections and compute totals
        if (!isset($repayments)) { $repayments = collect([]); }
        elseif (is_array($repayments)) { $repayments = collect($repayments); }

        $totalAmount = $repayments->sum(function($r){ return (float) data_get($r,'amount',0); });
        $totalPaid = $repayments->sum(function($r){ return (float) data_get($r,'paid_amount',0); });
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
                <h1>Repayments Report</h1>
                <div class="meta">
                    Date: <strong>{{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</strong>
                    &nbsp; | &nbsp;
                    Records: <strong>{{ $repayments->count() }}</strong>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <table>
            <thead>
                <tr>
                    <th style="width:4%;" class="text-center">#</th>
                    <th style="width:11%;">Loan ID</th>
                    <th style="width:30%;">Member</th>
                    <th style="width:12%;">Due Date</th>
                    <th style="width:11%;" class="text-right">Amount (₹)</th>
                    <th style="width:11%;" class="text-right">Paid (₹)</th>
                    <th style="width:11%;">Status</th>
                    <th style="width:10%;">Paid At</th>
                </tr>
            </thead>

            <tbody>
                @forelse($repayments as $key => $repayment)
                @php
                // support object/array shapes safely
                $loan = data_get($repayment, 'loan');
                $loanId = data_get($loan,'id') ?? data_get($repayment,'loan_id','-');
                $memberName = data_get($loan,'member.name') ?? data_get($repayment,'member.name') ?? '-';
                $dueDateRaw = data_get($repayment,'due_date') ?? data_get($repayment,'due_date');
                try {
                $dueDate = $dueDateRaw ? \Carbon\Carbon::parse($dueDateRaw)->format('d M Y') : '-';
                } catch (\Throwable $e) { $dueDate = '-'; }
                $amount = (float) data_get($repayment,'amount',0);
                $paid = (float) data_get($repayment,'paid_amount',0);
                $status = strtolower((string) data_get($repayment,'status',''));
                $paidAtRaw = data_get($repayment,'paid_at');
                try {
                $paidAt = $paidAtRaw ? \Carbon\Carbon::parse($paidAtRaw)->format('d M Y, h:i A') : '-';
                } catch (\Throwable $e) { $paidAt = '-'; }
                @endphp

                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>#{{ $loanId }}</td>
                    <td>{{ $memberName }}</td>
                    <td>{{ $dueDate }}</td>
                    <td class="text-right">₹ {{ number_format($amount, 2) }}</td>
                    <td class="text-right">₹ {{ number_format($paid, 2) }}</td>
                    <td>
                        @if($status === 'paid')
                        <span class="badge-paid">Paid</span>
                        @elseif($status === 'pending')
                        <span class="badge-pending">Pending</span>
                        @elseif($status === 'overdue')
                        <span class="badge-overdue">Overdue</span>
                        @else
                        {{ ucfirst($status ?: '-') }}
                        @endif
                    </td>
                    <td>{{ $paidAt }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center muted">No repayments found for the selected criteria.</td>
                </tr>
                @endforelse
            </tbody>

            @if($repayments->count() > 0)
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Totals</th>
                    <th class="text-right">₹ {{ number_format($totalAmount, 2) }}</th>
                    <th class="text-right">₹ {{ number_format($totalPaid, 2) }}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</body>

</html>