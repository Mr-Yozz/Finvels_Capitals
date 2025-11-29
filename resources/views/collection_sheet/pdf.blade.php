<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Collection Sheet - {{ $group->name ?? 'Group' }} - {{ $date }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 6px;
        }

        .sheet-image {
            margin-bottom: 10px;
            width: 120px;
            height: auto;
            float: left;
            border-radius: 100%;
            margin-right: 8px;
        }

        .clear {
            clear: both;
        }

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

        .col-id {
            width: 6%;
        }

        .col-member {
            width: 18%;
            text-align: left;
            padding-left: 8px;
        }

        .inst-line {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 2px 0;
            border-bottom: 1px solid #eee;
        }

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

        @media print {
            body {
                margin: 6mm;
            }
        }
    </style>
</head>

<body>

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

    <table class="collection">
        <thead>
            <tr>
                <th rowspan="2" class="col-id">ID</th>
                <th rowspan="2" class="col-member">MEMBER</th>

                <th colspan="2">LOAN BALANCES</th>
                <th colspan="2">DUES</th>

                <th rowspan="2">LOANS<br>
                    <span class="small">MEMB ADV | DUE DISB | SPOUSE KYC | PR | SANCHAY PRODUCT DUE | LP/PA/L</span>
                </th>
            </tr>

            <tr>
                <th class="small">LOAN INSTANCE</th>
                <th class="small">TOTAL</th>
                <th class="small">LOAN INSTANCE</th>
                <th class="small">TOTAL</th>
            </tr>
        </thead>

        <tbody>
            @foreach($rows as $r)
            <tr>
                <td class="mono">{{ $r['member_id'] }}</td>

                <td class="col-member">{{ $r['member_name'] }}</td>

                <td style="text-align:left; padding-left:6px;">
                    @forelse($r['loan_instances'] as $li)
                    <span class="inst-line">{{ $li }}</span>
                    @empty
                    <span class="inst-line">-</span>
                    @endforelse
                </td>

                <td>{{ number_format($r['loan_total_balance'],2) }}</td>

                <td style="text-align:left; padding-left:6px;">
                    @forelse($r['due_instances'] as $di)
                    <span class="inst-line">{{ $di }}</span>
                    @empty
                    <span class="inst-line">-</span>
                    @endforelse
                </td>

                <td>{{ number_format($r['due_total'],2) }}</td>

                <td style="text-align:left; padding-left:6px;">
                    <div class="small">
                        MEM ADV: {{ number_format($r['member_adv'],2) }}<br>
                        DUE DISB: {{ number_format($r['due_disb'],2) }}<br>
                        SPOUSE: {{ $r['spouse_kyc'] ?: '-' }}<br>
                        PR: {{ $r['pr'] ?? 0 }}<br>
                        SANCHAY: {{ $r['sanchay_due'] ?? 0 }}<br>
                        LP/PA/L: {{ $r['lp_pa_l'] ?? '-' }}
                    </div>
                </td>
            </tr>
            @endforeach

            <tr>
                <td colspan="3" style="text-align:right; font-weight:bold;">TOTAL</td>
                <td style="font-weight:bold;">
                    {{ number_format(collect($rows)->sum('loan_total_balance'),2) }}
                </td>

                <td style="text-align:right; font-weight:bold;">TOTAL DUE</td>
                <td style="font-weight:bold;">
                    {{ number_format(collect($rows)->sum('due_total'),2) }}
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div style="display:flex; gap:20px; margin-top:14px;">
        <div class="summary-box">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td>Due Collections</td>
                    <td style="text-align:right">{{ number_format($summary['due_collections'],2) }}</td>
                </tr>
                <tr>
                    <td>Other Collections</td>
                    <td style="text-align:right">{{ number_format($summary['other_collections'],2) }}</td>
                </tr>
                <tr>
                    <td>Total Collections</td>
                    <td style="text-align:right">{{ number_format($summary['total_collections'],2) }}</td>
                </tr>
                <tr>
                    <td>Due Disbursements</td>
                    <td style="text-align:right">{{ number_format($summary['due_disbursements'],2) }}</td>
                </tr>
                <tr>
                    <td>Other Disbursements</td>
                    <td style="text-align:right">{{ number_format($summary['other_disbursements'],2) }}</td>
                </tr>
                <tr>
                    <td>Total Disbursements</td>
                    <td style="text-align:right">{{ number_format($summary['total_disbursements'],2) }}</td>
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
                    <td>Amount Taken Back to Office</td>
                    <td style="text-align:right">{{ number_format($summary['amount_taken_back_office'],2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="sign-row">
        <div class="sign-item">Branch Leader</div>
        <div class="sign-item">Branch</div>
        <div class="sign-item">Cashier</div>
    </div>

</body>

</html>