<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Loan ID</th>
            <th>Member</th>
            <th>Due Date</th>
            <th>Amount</th>
            <th>Paid Amount</th>
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
            <td>{{ $repayment->due_date->format('d-M-Y') }}</td>
            <td>{{ number_format($repayment->amount, 2) }}</td>
            <td>{{ number_format($repayment->paid_amount, 2) }}</td>
            <td>{{ ucfirst($repayment->status) }}</td>
            <td>{{ $repayment->paid_at ? $repayment->paid_at->format('d M Y, h:i A') : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
