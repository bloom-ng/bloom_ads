<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Transaction Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .receipt-details {
            margin-bottom: 30px;
        }

        .detail-row {
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            color: #666;
        }

        .value {
            margin-top: 5px;
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
    <div class="container">
        <div class="header">
            <h1>Transaction Receipt</h1>
            <p>{{ $organization->name }}</p>
        </div>

        <div class="receipt-details">
            <div class="detail-row">
                <div class="label">Transaction Reference</div>
                <div class="value">{{ $transaction->reference }}</div>
            </div>

            <div class="detail-row">
                <div class="label">Date</div>
                <div class="value">{{ $transaction->created_at->format('M d, Y H:i') }}</div>
            </div>

            <div class="detail-row">
                <div class="label">Type</div>
                <div class="value">{{ ucfirst($transaction->type) }}</div>
            </div>

            <div class="detail-row">
                <div class="label">Description</div>
                <div class="value">{{ $transaction->description }}</div>
            </div>

            <div class="detail-row">
                <div class="label">Amount</div>
                <div class="value">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</div>
            </div>

            <div class="detail-row">
                <div class="label">Status</div>
                <div class="value">{{ ucfirst($transaction->status) }}</div>
            </div>

            @if ($transaction->rate && $transaction->source_currency)
                <div class="detail-row">
                    <div class="label">Exchange Rate</div>
                    <div class="value">
                        1 {{ $transaction->source_currency }} =
                        {{ number_format($transaction->rate, 4) }} {{ $transaction->currency }}
                    </div>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>This is a computer generated receipt and does not require a signature.</p>
        </div>
    </div>
</body>

</html>
