<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Collection Sheet - {{ $group->name ?? 'Group' }} - {{ $date }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .sheet-image {
            width: 100px;
            height: auto;
            border-radius: 100%;
            margin-bottom: 5px;
        }

        table.collection {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.collection th,
        table.collection td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 11px;
            word-wrap: break-word;
        }

        table.collection th {
            font-weight: 700;
            background: #f0f0f0;
        }

        .col-id {
            width: 6%;
        }

        .col-member {
            width: 18%;
            text-align: left;
        }

        .summary-box td {
            page-break-before: auto;
        }
    </style>
</head>

<body>

    {{-- ensure $filteredRows is a Collection for helper methods --}}
    @php
    // If controller passed array, convert to Collection. If already a Collection, keep it.
    if (isset($filteredRows) && is_array($filteredRows)) {
    $filteredRows = collect($filteredRows);
    } elseif (!isset($filteredRows)) {
    $filteredRows = collect([]);
    }

    // same for summary to avoid undefined index errors
    if (!isset($summary) || !is_array($summary)) {
    $summary = is_array($summary ?? null) ? $summary : (array) ($summary ?? []);
    }
    @endphp

    {{-- HEADER --}}
    <div class="header">
        <div style="width:100%; position:relative; margin-bottom:20px;">

            {{-- Left Corner Logo --}}
            @if(!empty($logoBase64))
            <img src="data:image/png;base64,{{ $logoBase64 }}"
                style="height:80px; position:absolute; left:0; top:0; border: radius 50%;">
            @endif

            {{-- Right Corner Logo --}}
            @if(!empty($LogoBase64))
            <img src="data:image/png;base64,{{ $LogoBase64 }}"
                style="height:80px; position:absolute; right:0; top:0; border: radius 50%;">
            @endif

            {{-- Center Heading --}}
            <div style="text-align:center;">
                <h2 style="margin:0;">FinVels Loans</h2>
                <h3 style="margin:0;">COLLECTION SHEET</h3>
            </div>

        </div>



        <!-- <h2 style="margin:0;">FinVels Loans</h2>
        <h3 style="margin:0;">COLLECTION SHEET</h3> -->

        <div style="margin-top:6px; font-size:12px;">
            {{ $group->branch->name ?? 'Branch' }} |
            <strong>{{ $group->name ?? '' }}</strong> |
            Date: <strong>{{ $date }}</strong> |
            Manager:
            {{-- safe manager name lookup: 'manager' variable from controller preferred, then relation --}}
            {{ $manager->name ?? $group->branch->manager->name ?? 'Not Assigned' ?? 'Not Assigned' }}
        </div>
    </div>

    {{-- TABLE --}}
    <table class="collection">
        <thead>
            <tr>
                <th rowspan="2" class="col-id">ID</th>
                <th rowspan="2" class="col-member">MEMBER</th>

                <th colspan="2">LOAN BALANCES</th>

                <th colspan="2">DUES</th>

                <!-- <th rowspan="2">Signature</th> -->
            </tr>

            <tr>
                <th style="text-align:left;">Loan Instance</th>
                <th>Total</th>
                <th>Due Amount</th>
                <th>Due Date</th>
            </tr>
        </thead>

        <tbody>
            @forelse($filteredRows as $r)
            <tr>
                <td>{{ $r['member_id'] ?? '-' }}</td>
                <td style="text-align:left;">{{ $r['member_name'] ?? '-' }}</td>

                <td style="text-align:left; font-size:7px;">
                    @if(!empty($r['loan_instances']) && is_array($r['loan_instances']))
                    @foreach($r['loan_instances'] as $li)
                    {!! nl2br(e($li)) !!}<br>
                    @endforeach
                    @elseif(!empty($r['loan_instances']) && $r['loan_instances'] instanceof \Illuminate\Support\Collection)
                    @foreach($r['loan_instances'] as $li)
                    {!! nl2br(e($li)) !!}<br>
                    @endforeach
                    @else
                    -
                    @endif
                </td>

                <td>{{ number_format((float)($r['loan_total_balance'] ?? 0), 2) }}</td>

                <td>{{ number_format((float)($r['next_due_amount'] ?? 0), 2) }}</td>

                <td>
                    @if(!empty($r['next_due_date']))
                    {{ \Carbon\Carbon::parse($r['next_due_date'])->format('d M Y') }}
                    @else
                    -
                    @endif
                </td>

                <!-- <td></td> -->
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:12px;">No records found for selected date.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- summary box --}}
    @php
    // Build filtered summary from the filteredRows collection (safe sums)
    $due_collections = (float) $filteredRows->sum(function($item){
    return (float) ($item['next_due_amount'] ?? 0);
    });

    $member_adv_sum = (float) $filteredRows->sum(function($item){
    return (float) ($item['member_adv'] ?? 0);
    });

    $pr_sum = (float) $filteredRows->sum(function($item){
    return (float) ($item['pr'] ?? 0);
    });

    $sanchay_sum = (float) $filteredRows->sum(function($item){
    return (float) ($item['sanchay_due'] ?? 0);
    });

    $other_collections = $member_adv_sum + $pr_sum + $sanchay_sum;

    $due_disbursements = (float) $filteredRows->sum(function($item){
    return (float) ($item['due_disb'] ?? 0);
    });

    $other_disbursements = 0.0;

    $filteredSummary = [
    'due_collections' => round($due_collections, 2),
    'other_collections' => round($other_collections, 2),
    'total_collections' => round($due_collections + $other_collections, 2),
    'due_disbursements' => round($due_disbursements, 2),
    'other_disbursements' => round($other_disbursements, 2),
    'total_disbursements' => round($due_disbursements + $other_disbursements, 2),
    ];

    // fallback summary values (from controller) for other metrics
    $applications_taken = isset($summary['applications_taken']) ? (int)$summary['applications_taken'] : 0;
    $no_loans_issued = isset($summary['no_loans_issued']) ? (int)$summary['no_loans_issued'] : 0;
    $absentees_defaults = isset($summary['absentees_defaults']) ? (int)$summary['absentees_defaults'] : 0;
    $amount_taken_back_office = isset($summary['amount_taken_back_office']) ? (float)$summary['amount_taken_back_office'] : 0.0;
    @endphp

    @if($filteredRows->count() > 0)
    <div style="display:flex; gap:20px; margin-top:14px;">
        <div class="summary-box" style="flex:0 0 50%;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:60%;">Due Collections</td>
                    <td style="text-align:right;">{{ number_format($filteredSummary['due_collections'], 2) }}</td>
                </tr>
                <tr>
                    <td>Other Collections</td>
                    <td style="text-align:right;">{{ number_format($filteredSummary['other_collections'], 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Total Collections</strong></td>
                    <td style="text-align:right;"><strong>{{ number_format($filteredSummary['total_collections'], 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Due Disbursements</td>
                    <td style="text-align:right;">{{ number_format($filteredSummary['due_disbursements'], 2) }}</td>
                </tr>
                <tr>
                    <td>Other Disbursements</td>
                    <td style="text-align:right;">{{ number_format($filteredSummary['other_disbursements'], 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Total Disbursements</strong></td>
                    <td style="text-align:right;"><strong>{{ number_format($filteredSummary['total_disbursements'], 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <div style="flex:1;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td>Applications Taken</td>
                    <td style="text-align:right">{{ number_format($applications_taken) }}</td>
                </tr>
                <tr>
                    <td>No. of Loans Issued</td>
                    <td style="text-align:right">{{ number_format($no_loans_issued) }}</td>
                </tr>
                <tr>
                    <td>Absentees/Defaults</td>
                    <td style="text-align:right">{{ number_format($absentees_defaults) }}</td>
                </tr>
                <tr>
                    <td>Amount taken back to Office</td>
                    <td style="text-align:right">{{ number_format($amount_taken_back_office, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
    @endif

    <div style="margin-top: 40px; width: 100%; text-align:center;">
        <table style="width: 90%; margin: 0 auto; border-collapse: collapse;">
            <tr>
                <td style="width:33%; padding: 20px 10px;">
                    <div style="border-top:1px solid #000; width:100%; margin:0 auto; padding-top:6px; font-weight:600;">
                        Member Leader
                    </div>
                </td>

                <td style="width:33%; padding: 20px 10px;">
                    <div style="border-top:1px solid #000; width:100%; margin:0 auto; padding-top:6px; font-weight:600;">
                        MRA Manager
                    </div>
                </td>

                <td style="width:33%; padding: 20px 10px;">
                    <div style="border-top:1px solid #000; width:100%; margin:0 auto; padding-top:6px; font-weight:600;">
                        Cashier
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>