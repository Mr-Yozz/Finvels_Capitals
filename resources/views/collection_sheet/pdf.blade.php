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
        padding: 6px;
        text-align: center;
        font-size: 11px;
    }

    table.collection th {
        font-weight: 700;
        background: #f0f0f0;
    }

    .col-id { width: 6%; }
    .col-member { width: 18%; text-align: left; }
</style>
</head>

<body>

{{-- HEADER --}}
<div class="header">
    @if($logoBase64)
        <img src="data:image/png;base64,{{ $logoBase64 }}" class="sheet-image">
    @endif

    <h2 style="margin:0;">FinVels Loans</h2>
    <h3 style="margin:0;">COLLECTION SHEET</h3>

    <div style="margin-top:6px; font-size:12px;">
        {{ $group->branch->name ?? 'Branch' }} |
        <strong>{{ $group->name ?? '' }}</strong> |
        Date: <strong>{{ $date }}</strong> |
        Manager: {{ $group->branch->manager->name ?? 'Not Assigned' }}
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

            <th rowspan="2">Signature</th>
        </tr>

        <tr>
            <th>Loan Instance</th>
            <th>Total</th>
            <th>Due Amount</th>
            <th>Due Date</th>
        </tr>
    </thead>

    <tbody>
        @foreach($filteredRows as $r)
        <tr>
            <td>{{ $r['member_id'] }}</td>
            <td>{{ $r['member_name'] }}</td>

            <td style="text-align:left;">
                @foreach($r['loan_instances'] as $li)
                    {{ $li }}<br>
                @endforeach
            </td>

            <td>{{ number_format($r['loan_total_balance'],2) }}</td>

            <td>{{ number_format($r['next_due_amount'],2) }}</td>

            <td>
                @if($r['next_due_date'])
                    {{ \Carbon\Carbon::parse($r['next_due_date'])->format('d M Y') }}
                @else
                    -
                @endif
            </td>

            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
