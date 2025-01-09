<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding-bottom: 20px;
            padding-left: 20px;
            padding-right: 20px;
            padding-top: 2px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .info-grid {
            display: block;
            margin-bottom: 20px;
        }

        .info-block {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8f8f8;
        }

        .amount-column {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>{{ $organization->name ?? 'N/A' }}</p>
        <p>Invoice #: {{ $invoiceNumber }}</p>
        <p>Date: {{ now()->format('Y-m-d') }}</p>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-block">
                <h3>Bill To:</h3>
                <p>{{ $organization->name ?? 'N/A' }}</p>
                {{-- <p>{{ $organization->address ?? 'N/A' }}</p> --}}
            </div>
            <div class="info-block">
                <h3>Pay To:</h3>
                <p>{{ $accountName }}</p>
                <p>Bank: {{ $bankName }}</p>
                <p>Account: {{ $accountNumber }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="amount-column">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Wallet Funding</td>
                <td class="amount-column">{{ number_format($amount, 2) }} {{ $currency }}</td>
            </tr>
            <tr>
                <td>VAT ({{ $vatRate }}%)</td>
                <td class="amount-column">{{ number_format($vat, 2) }} {{ $currency }}</td>
            </tr>
            <tr>
                <td>Service Fee ({{ $serviceFeeRate }}%)</td>
                <td class="amount-column">{{ number_format($serviceFee, 2) }} {{ $currency }}</td>
            </tr>
            <tr class="total-row">
                <td>Total</td>
                <td class="amount-column">{{ number_format($total, 2) }} {{ $currency }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Thank you for your business!</p>
    </div>
</body>

</html>
