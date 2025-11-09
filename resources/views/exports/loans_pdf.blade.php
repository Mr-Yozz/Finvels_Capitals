<!DOCTYPE html>
<html>

<head>
    <title>Loans Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <h3>Loans Report</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Branch</th>
                <th>Principal</th>
                <th>Interest Rate</th>
                <th>Tenure (Months)</th>
                <th>EMI</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $key => $loan)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $loan->member->name ?? '-' }}</td>
                <td>{{ $loan->branch->name ?? '-' }}</td>
                <td>{{ number_format($loan->principal, 2) }}</td>
                <td>{{ $loan->interest_rate }}%</td>
                <td>{{ $loan->tenure_months }}</td>
                <td>{{ number_format($loan->monthly_emi, 2) }}</td>
                <td>{{ ucfirst($loan->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>