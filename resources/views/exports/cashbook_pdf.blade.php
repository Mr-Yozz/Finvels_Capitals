<html>

<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <div style="text-align:center;">
        <img src="{{ $logo }}" height="80">
        <h3>Cashbook Report - {{ $date }}</h3>
    </div>

    <table>
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
            <th>Closing Balance</th>
            <th>{{ $cashbook->closing_balance }}</th>
        </tr>
    </table>

    <br><br>
    <h3>Today's Loan Disbursement</h3>

    <table>
        <tr>
            <th>Loan ID</th>
            <th>Member</th>
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

    <br><br>
    <h3>Denomination Table</h3>

    <table>
        <tr>
            <th>Note</th>
            <th>Count</th>
            <th>Total</th>
        </tr>
        <tr>
            <td>2000</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>500</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>200</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>100</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>50</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>20</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>10</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Coins</td>
            <td></td>
            <td></td>
        </tr>
    </table>

</body>

</html>