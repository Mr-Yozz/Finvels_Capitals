<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Branches Report - {{ $date ?? now()->toDateString() }}</title>

    <style>
        /* Page settings (works with dompdf/snappy) */
        @page {
            margin: 20mm 12mm;
        }

        body {
            font-family: "DejaVu Sans", "Helvetica", Arial, sans-serif;
            font-size: 12px;
            color: #111;
            margin: 0;
        }

        /* Header area */
        .header {
            margin-bottom: 8px;
        }

        .header-row {
            width: 100%;
            position: relative;
            min-height: 100px;
            /* reserve space for logos */
            margin-bottom: 6px;
        }

        /* Logo styles */
        .logo {
            height: 80px;
            width: auto;
            object-fit: contain;
            border-radius: 50%;
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

        /* Center title */
        .title-wrap {
            text-align: center;
            padding-top: 6px;
            /* shift down a little under logos */
        }

        .title-wrap h1 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 0.5px;
        }

        .title-wrap h2 {
            margin: 2px 0 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
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

        /* repeat header on each PDF page */
        tfoot {
            display: table-footer-group;
        }

        th,
        td {
            border: 1px solid #bbb;
            padding: 6px 8px;
            vertical-align: middle;
            font-size: 11px;
        }

        th {
            background: #f6f6f6;
            font-weight: 700;
            text-align: left;
        }

        td.text-center,
        th.text-center {
            text-align: center;
        }

        td.text-right,
        th.text-right {
            text-align: right;
        }

        /* zebra rows for readability */
        tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        tbody tr:nth-child(even) {
            background: #fcfcfc;
        }

        /* small screens fallback (not really needed for PDF but good for preview) */
        @media (max-width: 600px) {
            .title-wrap h1 {
                font-size: 16px;
            }

            .logo {
                height: 56px;
            }

            th,
            td {
                padding: 6px;
                font-size: 10px;
            }
        }

        /* footer note */
        .report-meta {
            margin-top: 10px;
            font-size: 11px;
            color: #444;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-row">

            {{-- Left Corner Logo --}}
            @if(!empty($logoBase64))
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="left logo" class="logo logo-left">
            @endif

            {{-- Right Corner Logo --}}
            @if(!empty($LogoBase64))
            <img src="data:image/png;base64,{{ $LogoBase64 }}" alt="right logo" class="logo logo-right">
            @endif

            {{-- Center Heading --}}
            <div class="title-wrap">
                <h1>Branches Report</h1>
                <h2>{{ $organizationName ?? ($group->branch->name ?? '') }}</h2>
                <div style="font-size:11px; color:#666; margin-top:4px;">
                    Date: {{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    <table>
        <thead>
            <tr>
                <th style="width:5%;" class="text-center">#</th>
                <th style="width:22%;">Branch Name</th>
                <th style="width:43%;">Address</th>
                <th style="width:10%;" class="text-center">Total Groups</th>
                <th style="width:10%;" class="text-center">Total Loans</th>
                <th style="width:10%;" class="text-center">Total Users</th>
            </tr>
        </thead>

        <tbody>
            @forelse($branches as $key => $branch)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $branch->name ?? '-' }}</td>
                <td>
                    @if(!empty($branch->address))
                    {{ $branch->address }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-center">{{ number_format($branch->groups_count ?? 0) }}</td>
                <td class="text-center">{{ number_format($branch->loans_count ?? 0) }}</td>
                <td class="text-center">{{ number_format($branch->users_count ?? 0) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No branches found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- optional meta/footer --}}
    <div class="report-meta">
        Generated by: {{ $generatedBy ?? auth()->user()->name ?? 'System' }} |
        Total Branches: {{ number_format($branches->count() ?? (is_array($branches) ? count($branches) : 0)) }}
    </div>

</body>

</html>