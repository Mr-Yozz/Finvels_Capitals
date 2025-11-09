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
            <td>{{ $key + 1 }}</td>
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
