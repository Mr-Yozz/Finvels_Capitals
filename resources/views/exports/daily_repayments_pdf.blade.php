<!DOCTYPE html>
<html>

<head>
    <title>Daily Reports</title>
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
    <h3>Daily Reports</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Loan ID</th>
                <th>Branch</th>
                <th>Due Date</th>
                <th>Due Amount</th>
                <th>Paid Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($repayments as $key => $repayment)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $repayment->loan->member->name ?? '-' }}</td>
                <td>#{{ $repayment->loan->id }}</td>
                <td>{{ $repayment->loan->branch->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($repayment->due_date)->format('d M Y') }}</td>
                <td>{{ number_format($repayment->amount, 2) }}</td>
                <td>{{ number_format($repayment->paid_amount, 2) }}</td>
                <td>{{ ucfirst($repayment->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>