<table>
    <tr>
        <th colspan="2">
            <strong>Cashbook Report - {{ $date }}</strong>
        </th>
    </tr>

    <tr>
        <td>Opening Balance</td>
        <td>{{ $cashbook->opening_balance }}</td>
    </tr>
    <tr>
        <td>Total Collection</td>
        <td>{{ $cashbook->total_collection }}</td>
    </tr>
    <tr>
        <td>Deposit</td>
        <td>{{ $cashbook->deposit }}</td>
    </tr>
    <tr>
        <td>Expenses</td>
        <td>{{ $cashbook->expenses }}</td>
    </tr>
    <tr>
        <td><strong>Closing Balance</strong></td>
        <td><strong>{{ $cashbook->closing_balance }}</strong></td>
    </tr>
</table>

<br><br>

<table>
    <tr>
        <th colspan="3"><strong>Today's Loan Disbursement</strong></th>
    </tr>
    <tr>
        <th>Loan ID</th>
        <th>Member Name</th>
        <th>Loan Amount</th>
    </tr>

    @foreach($loans as $loan)
    <tr>
        <td>{{ $loan->id }}</td>
        <td>{{ $loan->member->name }}</td>
        <td>{{ $loan->principal }}</td>
    </tr>
    @endforeach
</table>