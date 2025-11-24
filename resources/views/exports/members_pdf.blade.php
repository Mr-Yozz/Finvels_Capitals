<!DOCTYPE html>
<html>

<head>
    <title>Members Report</title>
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
    <h3>Members Report</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>Aadhaar</th>
                <th>PAN</th>
                <th>Group</th>
                <th>Branch</th>
                <th>Bank Name</th>
                <th>Account No</th>
                <th>IFSC Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $key => $member)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $member->name }}</td>
                <td>{{ $member->mobile }}</td>
                <td>{{ $member->aadhaar_encrypted }}</td>
                <td>{{ $member->pan_encrypted }}</td>
                <td>{{ $member->group->name ?? '-' }}</td>
                <td>{{ $member->group->branch->name ?? '-' }}</td>
                <td>{{ $member->bank_name ?? '-' }}</td>
                <td>{{ $member->account_number ?? '-' }}</td>
                <td>{{ $member->ifsc_code ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>