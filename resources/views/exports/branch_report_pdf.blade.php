<!DOCTYPE html>
<html>

<head>
    <title>Branch Reports</title>
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
    <h3>Branch Reports</h3>

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
</body>

</html>