<!DOCTYPE html>
<html>

<head>
    <title>Invoice PDF</title>
    <style>
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
    </style>
</head>

<body>

    <h2 style="margin-bottom: 10px;">Invoice #{{ $invoice->invoice_no }}</h2>

    <table style="width:100%; border-collapse: collapse; margin-bottom: 15px;">
        <tr>
            <td><strong>Member ID:</strong></td>
            <td>{{ $invoice->loan->member->id ?? '-' }}</td>

            <td><strong>Kendra Name:</strong></td>
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
            <td><strong>ROI / APR:</strong></td>
            <td>{{ $invoice->loan->interest_rate }}% / {{ $invoice->loan->apr ?? '15.00' }}%</td>

            <td><strong>Term of Loan:</strong></td>
            <td>{{ $invoice->loan->tenure_months }} (Monthly)</td>
        </tr>

        <tr>
            <td><strong>Processing Fee:</strong></td>
            <td> {{ number_format($invoice->loan->processing_fee ?? 0,2) }}</td>

            <td><strong>Insurance Premium:</strong></td>
            <td> {{ number_format($invoice->loan->insurance ?? 10,2) }}</td>
        </tr>

        <tr>
            <td><strong>Phone:</strong></td>
            <td>{{ $invoice->loan->member->phone ?? '-' }}</td>

            <td><strong>Invoice Date:</strong></td>
            <td>{{ $invoice->created_at->format('d M Y') }}</td>
        </tr>
    </table>

    <br><br>

    <table width="100%" border="1" cellspacing="0" cellpadding="6">
        <thead>
            <tr style="background:#e9e9e9; font-weight:bold;">
                <th>Inst. No</th>
                <th>Due Date</th>
                <th>Principal</th>
                <th>Interest</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($invoice->lines as $line)
            <tr>
                <td>{{ $line->inst_no }}</td>
                <td>{{ \Carbon\Carbon::parse($line->due_date)->format('d M Y') }}</td>
                <td> {{ number_format($line->principal,2) }}</td>
                <td> {{ number_format($line->interest,2) }}</td>
                <td> {{ number_format($line->total,2) }}</td>
                <td>{{ $line->status ?? 'Pending' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>


</html>