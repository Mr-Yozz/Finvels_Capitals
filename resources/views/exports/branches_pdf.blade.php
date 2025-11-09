<!DOCTYPE html>
<html>

<head>
    <title>Branches Report</title>
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
    <h3>Branches Report</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Branch Name</th>
                <th>Address</th>
                <th>Total Groups</th>
                <th>Total Loans</th>
                <th>Total Users</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $key => $branch)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $branch->name }}</td>
                <td>{{ $branch->address }}</td>
                <td class="text-center">{{ $branch->groups_count }}</td>
                <td class="text-center">{{ $branch->loans_count }}</td>
                <td class="text-center">{{ $branch->users_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>