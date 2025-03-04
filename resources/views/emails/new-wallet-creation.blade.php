<!DOCTYPE html>
<html>
<head>
    <title>New Wallet Created</title>
</head>
<body>
    <h1>New Wallet Created</h1>
    <p>A new wallet has been created with the following details:</p>

    <h2>User Details:</h2>
    <ul>
        <li>Name: {{ $userName }}</li>
        <li>Email: {{ $userEmail }}</li>
        <li>Business Name: {{ $businessName }}</li>
    </ul>

    <h2>Wallet Details:</h2>
    <ul>
        <li>Organization: {{ $organizationName }}</li>
        <li>Currency: {{ $walletCurrency }}</li>
    </ul>

    <p>
        <a href="{{ config('app.url') }}/admin/wallets" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View Wallet</a>
    </p>

    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>