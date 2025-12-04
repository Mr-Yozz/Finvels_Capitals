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
    <div class="header">
        <div style="width:100%; position:relative; margin-bottom:20px;">

            {{-- Left Corner Logo --}}
            @if(!empty($logoBase64))
            <img src="data:image/png;base64,{{ $logoBase64 }}"
                style="height:80px; position:absolute; left:0; top:0; border: radius 50%;">
            @endif

            {{-- Right Corner Logo --}}
            @if(!empty($LogoBase64))
            <img src="data:image/png;base64,{{ $LogoBase64 }}"
                style="height:80px; position:absolute; right:0; top:0; border: radius 50%;">
            @endif

            <!-- {{-- Center Heading --}}
            <div style="text-align:center;">
                <h2 style="margin:0;">FinVels Loans</h2>
                <h3 style="margin:0;">COLLECTION SHEET</h3>
            </div> -->

        </div>

        <div style="margin-top:6px; font-size:12px;">
            {{ $group->branch->name ?? 'Branch' }} |
            <strong>{{ $group->name ?? '' }}</strong> |
            Date: <strong>{{ $date }}</strong> |
            Manager:
            {{-- safe manager name lookup: 'manager' variable from controller preferred, then relation --}}
            {{ $manager->name ?? $group->branch->manager->name ?? 'Not Assigned' ?? 'Not Assigned' }}
        </div>
    </div>
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