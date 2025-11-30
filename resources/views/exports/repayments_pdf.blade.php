<!DOCTYPE html>
<html>

<head>
    <title>Repayments Report</title>
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

        .text-right {
            text-align: right;
        }

        .badge-paid {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .badge-pending {
            background-color: #ffc107;
            color: black;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .badge-overdue {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <h3>Repayments Report</h3>
    <div style="text-align:left; margin-bottom:20px;">
        @if(!empty($logo))
        <img src="data:image/jpeg;base64,{{ $logo }}"
            style="height:80px; width:80px; border-radius:50%; object-fit:cover;">
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Loan ID</th>
                <th>Member</th>
                <th>Due Date</th>
                <th>Amount (₹)</th>
                <th>Paid Amount (₹)</th>
                <th>Status</th>
                <th>Paid At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($repayments as $key => $repayment)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>#{{ $repayment->loan->id ?? '-' }}</td>
                <td>{{ $repayment->loan->member->name ?? '-' }}</td>
                <td>{{ $repayment->due_date->format('d M Y') }}</td>
                <td class="text-right">{{ number_format($repayment->amount, 2) }}</td>
                <td class="text-right">{{ number_format($repayment->paid_amount, 2) }}</td>
                <td>
                    @if($repayment->status == 'paid')
                    <span class="badge-paid">Paid</span>
                    @elseif($repayment->status == 'pending')
                    <span class="badge-pending">Pending</span>
                    @elseif($repayment->status == 'overdue')
                    <span class="badge-overdue">Overdue</span>
                    @endif
                </td>
                <td>{{ $repayment->paid_at ? $repayment->paid_at->format('d M Y, h:i A') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>