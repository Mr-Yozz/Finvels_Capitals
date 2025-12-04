<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Members Report - {{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</title>

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

        .container {
            padding: 12px;
        }

        /* Header area */
        .header-row {
            width: 100%;
            position: relative;
            min-height: 110px;
            margin-bottom: 10px;
            box-sizing: border-box;
            padding-top: 6px;
        }

        .logo {
            height: 78px;
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
            padding-top: 8px;
        }

        .title-wrap h1 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.6px;
        }

        .meta {
            margin-top: 6px;
            font-size: 11px;
            color: #555;
        }

        .header-info {
            margin-top: 8px;
            font-size: 13px;
            color: #222;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: auto;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        /* repeat on page break */
        tfoot {
            display: table-footer-group;
        }

        th,
        td {
            border: 1px solid #e0e0e0;
            padding: 10px 8px;
            vertical-align: middle;
            font-size: 12px;
            overflow: hidden;
        }

        th {
            background: #f7f7f7;
            font-weight: 700;
            text-align: left;
        }

        /* column widths */
        th.col-no {
            width: 4%;
        }

        th.col-name {
            width: 22%;
        }

        th.col-mobile {
            width: 12%;
            white-space: nowrap;
        }

        th.col-aadhaar {
            width: 16%;
            word-break: break-word;
        }

        th.col-pan {
            width: 12%;
            white-space: nowrap;
        }

        th.col-bank {
            width: 12%;
        }

        th.col-ac {
            width: 12%;
            word-break: break-all;
        }

        th.col-ifsc {
            width: 10%;
            white-space: nowrap;
        }

        td.small {
            font-size: 11px;
            color: #222;
        }

        tbody tr:nth-child(even) {
            background: #fbfbfb;
        }

        /* role classes */
        .role-leader {
            font-size: 15px;
            font-weight: 700;
        }

        .role-sub_leader {
            font-size: 13px;
            font-weight: 600;
        }

        .role-member {
            font-size: 12px;
            font-weight: 500;
        }

        /* keep table header sticky in browser preview (harmless in PDF) */
        thead th {
            position: sticky;
            top: 0;
        }

        @media (max-width:700px) {
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
    <div class="container">
        @php
        // safe conversions
        if (!isset($members)) { $members = collect([]); }
        elseif (is_array($members)) { $members = collect($members); }

        // possible variables you may pass:
        // $group (object) -> report for a group
        // $branch (object) -> report for a branch
        // $member (object) -> single member context
        $group = $group ?? null;
        $branch = $branch ?? null;
        $totalCount = $members->count();
        @endphp

        {{-- HEADER --}}
        <div class="header-row">
            @if(!empty($logoBase64))
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="left logo" class="logo logo-left">
            @endif

            @if(!empty($LogoBase64))
            <img src="data:image/png;base64,{{ $LogoBase64 }}" alt="right logo" class="logo logo-right">
            @endif

            <div class="title-wrap">
                <h1>Members Report</h1>
                <div class="meta">
                    Total Members: <strong>{{ number_format($totalCount) }}</strong>
                    &nbsp;|&nbsp;
                    Date: <strong>{{ \Carbon\Carbon::parse($date ?? now())->format('d M Y') }}</strong>
                </div>

                {{-- top-level group/branch info --}}
                <div class="header-info">
                    @if($group)
                    Group: <strong>{{ data_get($group,'name','-') }}</strong>
                    @else
                    Group: <strong>All Groups</strong>
                    @endif

                    &nbsp;&nbsp;|&nbsp;&nbsp;

                    @if($branch)
                    Branch: <strong>{{ data_get($branch,'name','-') }}</strong>
                    @else
                    Branch: <strong>All Branches</strong>
                    @endif
                </div>
            </div>
        </div>

        {{-- MEMBERS TABLE --}}
        <table>
            <thead>
                <tr>
                    <th class="col-no">#</th>
                    <th class="col-name">Name</th>
                    <th class="col-mobile">Mobile</th>
                    <th class="col-aadhaar">Aadhaar</th>
                    <th class="col-pan">PAN</th>
                    <th class="col-bank">Bank</th>
                    <th class="col-ac">Account No</th>
                    <th class="col-ifsc">IFSC</th>
                </tr>
            </thead>

            <tbody>
                @forelse($members as $key => $member)
                @php
                $role = strtolower($member->role ?? 'member');
                $roleClass = $role === 'leader' ? 'role-leader' : ($role === 'sub_leader' ? 'role-sub_leader' : 'role-member');
                $aadhaar = data_get($member,'aadhaar_encrypted') ?: '';
                $pan = data_get($member,'pan_encrypted') ?: '-';
                @endphp

                <tr>
                    <td class="text-center small">{{ $key + 1 }}</td>

                    <td>
                        <span class="{{ $roleClass }}">{{ data_get($member,'name','-') }}</span>
                    </td>

                    <td class="small" style="white-space:nowrap;">{{ data_get($member,'mobile','-') }}</td>

                    {{-- Aadhaar displayed in groups of 4 digits for readability --}}
                    <td class="small" style="word-break:break-word; white-space:normal;">
                        @if($aadhaar)
                        {!! nl2br(e(preg_replace('/(\d{4})/', '$1 ', trim($aadhaar)))) !!}
                        @else
                        -
                        @endif
                    </td>

                    <td class="small" style="white-space:nowrap;">{{ $pan }}</td>

                    <td class="small">{{ data_get($member,'bank_name') ?? '-' }}</td>

                    <td class="small" style="word-break:break-all;">{{ data_get($member,'account_number') ?? '-' }}</td>

                    <td class="small" style="white-space:nowrap;">{{ data_get($member,'ifsc_code') ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No members found.</td>
                </tr>
                @endforelse
            </tbody>

            @if($members->count() > 0)
            <tfoot>
                <tr>
                    <th colspan="7" style="text-align:right; padding-right:12px;">Total Members</th>
                    <th class="text-center">{{ number_format($totalCount) }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</body>

</html>