<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Branch</th>
            <th>Total Loans</th>
            <th>Total Due</th>
            <th>Total Paid</th>
            <th>Outstanding</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportData as $key => $branch)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $branch['branch_name'] }}</td>
            <td>{{ $branch['total_loans'] }}</td>
            <td>{{ number_format($branch['total_due'], 2) }}</td>
            <td>{{ number_format($branch['total_paid'], 2) }}</td>
            <td>{{ number_format($branch['outstanding'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
