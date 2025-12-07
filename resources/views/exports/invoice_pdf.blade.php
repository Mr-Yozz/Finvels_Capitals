<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Repayment Schedule - A4 Format</title>
    <style>
        /* General Styles for the document */
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 10mm;
            /* General padding */
        }

        /* A4 Print Optimization */
        @page {
            size: A4;
            /* Define the page size as A4 */
            margin: 10mm;
            /* Minimal margins for print */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
        }

        /* Header Tables */
        .header-table,
        .main-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .header-table td {
            padding: 3px;
            vertical-align: top;
            font-size: 9pt;
        }

        .loan-details td {
            padding: 3px 0;
        }

        /* Two-Column Table Container for Repayment Schedule */
        .table-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .table-col {
            width: 100%;
            /* Ensures tables sit side-by-side on A4 */
        }

        /* Repayment Table Styles */
        .repayment-table {
            width: 100%;
            border-collapse: collapse;
            /* Prevents the table from being split across columns if possible */
            page-break-inside: avoid;
        }

        .repayment-table th,
        .repayment-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: right;
            font-size: 7pt;
            /* Smaller font for A4 density */
            white-space: nowrap;
            /* Keeps data in one line */
        }

        .repayment-table th {
            text-align: center;
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .repayment-table td:nth-child(2) {
            /* Date column */
            text-align: center;
        }

        .repayment-table td:nth-child(1),
        .repayment-table td:nth-child(7) {
            /* Inst. No and Signature */
            text-align: center;
        }

        /* Specific header cell styling */
        .main-data-table td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
            font-size: 9pt;
        }

        .main-data-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .main-data-table table td {
            border: none;
            padding: 0;
            font-size: 9pt;
        }

        .main-data-table .split-cell td:first-child {
            border-right: 1px solid black;
        }

        .header-row {
            width: 100%;
            position: relative;
            min-height: 72px;
            /* reserve space for logos */
            margin-bottom: 8px;
        }

        .logo {
            height: 56px;
            width: auto;
            object-fit: contain;
            display: block;
            border-radius: 100%;
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
    </style>
</head>

<body>
    @php
    $loan = $invoice->loan ?? null;
    $member = $loan->member ?? null;
    $branch = $loan->branch ?? null;
    // totals from invoice lines
    $lines = $invoice->lines ;

    $totalPrincipal = $lines->sum(fn($l)=> (float) data_get($l,'principal',0));
    $totalInterest = $lines->sum(fn($l)=> (float) data_get($l,'interest',0));
    $totalLineTotal = $lines->sum(fn($l)=> (float) data_get($l,'total',0));
    @endphp

    <table class="header-table">
        <tr>
            <div class="header-row">
                @if(!empty($logoBase64))
                <img src="data:image/png;base64,{{ $logoBase64 }}" alt="left logo" class="logo logo-left">
                @endif

                @if(!empty($LogoBase64))
                <img src="data:image/png;base64,{{ $LogoBase64 }}" alt="right logo" class="logo logo-right">
                @endif
            </div>
        </tr>
    </table>

    <h2 style="margin-bottom: 10px;">Invoice #{{ $invoice->invoice_no }}</h2>
    <table style="width:100%; border-collapse: collapse; margin-bottom: 15px;">
        <tr>
            <td><strong>Member ID:</strong></td>
            <td>{{ $invoice->loan->member->id ?? '-' }}</td>

            <td><strong>Branch Name:</strong></td>
            <td>{{ $invoice->loan->branch->name ?? '-' }}</td>
        </tr>

        <tr>
            <td><strong>Product Name:</strong></td>
            <td>{{ $invoice->loan->product->name ?? 'Business Loan' }}</td>

            <td><strong>Loan Purpose:</strong></td>
            <td>{{ $invoice->loan->purpose ?? 'N/A' }}</td>
        </tr>

        <tr>
            <td><strong>Member Name:</strong></td>
            <td>{{ $invoice->loan->member->name }}</td>

            <td><strong>Loan Acc Num:</strong></td>
            <td>{{ $invoice->loan->id }}</td>
        </tr>

        <tr>
            <td><strong>Moratorium:</strong></td>
            <td>{{ $invoice->loan->moratorium ?? '0' }}</td>

            <td><strong>Disbursement Date:</strong></td>
            <td>{{ optional($invoice->loan->disbursed_at)->format('d M Y') }}</td>
        </tr>

        <tr>
            <td><strong>Spouse Name:</strong></td>
            <td>{{ $invoice->loan->member->spouse_name ?? '-' }}</td>

            <td><strong>Loan Amount:</strong></td>
            <td>₹ {{ number_format($invoice->loan->principal,2) }}</td>
        </tr>

        <tr>
            <td><strong>Term of Loan:</strong></td>
            <td>{{ $invoice->loan->tenure_months }}</td>

            <td><strong>Processing Fee:</strong></td>
            <td>
                @php
                // Safely retrieve the fee, defaulting to 0 if null.
                $feeValue = $invoice->loan->processing_fee ?? 0;

                // Format the number to 2 decimal places.
                $formattedFee = number_format((float) $feeValue, 2);
                @endphp

                {{ $formattedFee }}%
            </td>
        </tr>

        <tr>
            <td><strong>Insurance Premium:</strong></td>
            <td> {{ number_format($invoice->loan->insurance ?? 10,2) }}</td>

            <td><strong>Phone:</strong></td>
            <td>{{ $invoice->loan->member->phone ?? '-' }}</td>
        </tr>

        <tr>
            <td><strong>Invoice Date:</strong></td>
            <td>{{ $invoice->created_at->format('d M Y') }}</td>

            <td><strong></strong></td>
            <td></td>
        </tr>
    </table>

    <br><br>


    <!-- <table class="main-data-table" style="width: 100%;">
        <tr>
            <td style="width: 20%;">Processing Fees/Pre-FET Upload Collection</td>
            <td style="width: 10%;">Rs 825</td>
            <td style="width: 20%;">(1.5% of Loan amt exclude GST)</td>
            <td style="width: 15%;">Insurance Premium
                <table class="split-cell">
                    <tr>
                        <td>Member</td>
                        <td>Spouse</td>
                    </tr>
                </table>
            </td>
            <td style="width: 10%;">Rs 1430</td>
            <td style="width: 25%;">Phone: 7944119040</td>
        </tr>
    </table> -->

    <div class="table-container">
        <div class="table-col">
            <table class="repayment-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Inst. No.</th>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 15%;">Principal</th>
                        <th style="width: 15%;">Interest</th>
                        <th style="width: 15%;">Total</th>
                        <th style="width: 15%;">Prin OS</th>
                        <th style="width: 15%;">KM Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lines as $line)
                    <tr>
                        <td>{{ $line->inst_no }}</td>
                        <td>{{ \Carbon\Carbon::parse($line->due_date)->format('d M Y') }}</td>
                        <td class="right-align">{{ number_format($line->principal,2) }}</td>
                        <td class="right-align">{{ number_format($line->interest,2) }}</td>
                        <td class="right-align">{{ number_format($line->total,2) }}</td>
                        <td class="right-align">{{ number_format($line->os,2) }}</td>
                        <td></td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        {{-- <!-- <div class="table-col">
            <table class="repayment-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">Inst. No.</th>
                        <th style="width: 5%;">Date</th>
                        <th style="width: 5%;">Principal</th>
                        <th style="width: 5%;">Interest</th>
                        <th style="width: 5%;">Total</th>
                        <th style="width: 5%;">Prin OS</th>
                        <th style="width: 5%;">KM Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($right_rows as $line)
                    <tr>
                        <td>{{ $line->inst_no }}</td>
        <td>{{ \Carbon\Carbon::parse($line->due_date)->format('d M Y') }}</td>
        <td class="right-align">{{ number_format($line->principal,2) }}</td>
        <td class="right-align">{{ number_format($line->interest,2) }}</td>
        <td class="right-align">{{ number_format($line->total,2) }}</td>
        <td class="right-align">{{ number_format($line->os,2) }}</td>
        <td></td>
        </tr>
        @endforeach

        </tbody>
        </table>
    </div> --> --}}
    </div>

    <table class="main-data-table" style="width: 100%; border: 1px solid black; margin-top: 10px;">
        <tr>
            <td style="border-right: 1px solid black; width: 50%; padding: 5px; text-align: left; border-top: none;">
                Loan Closed Date:
            </td>
            <td style="width: 50%; padding: 5px; text-align: left; border-top: none;">
                KM Signature
            </td>
        </tr>
    </table>

</body>

</html>