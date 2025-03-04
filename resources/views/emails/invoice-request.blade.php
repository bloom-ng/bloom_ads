<!DOCTYPE html>
<html>
<head>
    <title>New Invoice Request</title>
</head>
<body>
    <h1>New Invoice Request</h1>
    <p>A new invoice has been requested with the following details:</p>

    <h2>User Details:</h2>
    <ul>
        <li>Name: {{ $userName }}</li>
        <li>Email: {{ $userEmail }}</li>
        <li>Business Name: {{ $businessName }}</li>
    </ul>

    <h2>Invoice Details:</h2>
    <ul>
        <li>Amount: {{ number_format($amount, 2) }}</li>
        <li>Currency: {{ $currency }}</li>
        <li>Description: {{ $description }}</li>
    </ul>

    <p>
        <a href="{{ config('app.url') }}/admin/invoices" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View Invoice Requests</a>
    </p>

    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>
