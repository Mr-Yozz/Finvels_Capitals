<!DOCTYPE html>
<html>

<head>
    <title>Groups Report</title>
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
    <h3>Groups Report</h3>
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
                <th>Group Name</th>
                <th>Branch</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groups as $key => $group)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $group->name }}</td>
                <td>{{ $group->branch->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>