<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Groups Report - {{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</title>

    <style>
        @page {
            margin: 18mm 12mm;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 12px;
            color: #111;
            margin: 0;
        }

        /* Header */
        .header-row {
            width: 100%;
            position: relative;
            min-height: 90px;
            /* reserve space for logos */
            margin-bottom: 8px;
        }

        .logo {
            height: 72px;
            width: auto;
            object-fit: contain;
            display: block;
        }

        .logo-left {
            position: absolute;
            left: 0;
            top: 0;
        }

        .logo-right {
            position: absolute;
            right: 0;
            top: 0;
        }

        .title-wrap {
            text-align: center;
            padding-top: 6px;
        }

        .title-wrap h1 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 0.4px;
        }

        .meta {
            margin-top: 4px;
            font-size: 11px;
            color: #555;
        }

        .muted {
            color: #666;
            font-size: 11px;
            margin-top: 2px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        /* repeat header on page break */
        tfoot {
            display: table-footer-group;
        }

        th,
        td {
            border: 1px solid #bbb;
            padding: 8px 10px;
            vertical-align: middle;
            font-size: 12px;
        }

        th {
            background: #f6f6f6;
            font-weight: 700;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        tbody tr:nth-child(even) {
            background: #fbfbfb;
        }

        @media (max-width:600px) {
            .logo {
                height: 56px;
            }

            th,
            td {
                padding: 6px;
                font-size: 11px;
            }

            .title-wrap h1 {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    @php
    // Ensure collections / safe fallbacks
    if (!isset($groups)) { $groups = collect([]); }
    elseif (is_array($groups)) { $groups = collect($groups); }

    $member = $member ?? null;
    @endphp

    <div class="header-row">
        @if(!empty($logoBase64))
        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="left logo" class="logo logo-left">
        @endif

        @if(!empty($LogoBase64))
        <img src="data:image/png;base64,{{ $LogoBase64 }}" alt="right logo" class="logo logo-right">
        @endif

        <div class="title-wrap">
            <h1>Groups Report</h1>
            <div class="meta">
                @if($member)
                Member: <strong>{{ data_get($member,'name','-') }}</strong> &nbsp;|&nbsp;
                @endif
                Date: <strong>{{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</strong>
            </div>

            @if(!empty($member->id))
            <div class="muted">Member ID: {{ $member->id }}</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:6%;" class="text-center">#</th>
                <th style="width:52%;">Group Name</th>
                <th style="width:42%;">Branch</th>
            </tr>
        </thead>

        <tbody>
            @forelse($groups as $key => $group)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $group->name ?? '-' }}</td>
                <td>{{ data_get($group, 'branch.name', '-') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center muted">No groups found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>