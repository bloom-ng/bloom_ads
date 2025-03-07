@extends('layouts.email')

@section('content')
    <h1>Wallet Funded</h1>

    <p>Dear {{ $wallet->organization->users->first()->name }},</p>

    <p>Your wallet has been credited with {{ $wallet->currency }} {{ number_format($amount, 2) }} by the administrator.</p>

    <h2>Transaction Details:</h2>
    <ul>
        <li>Amount: {{ $wallet->currency }} {{ number_format($amount, 2) }}</li>
        <li>Description: {{ $description }}</li>
        <li>Wallet ID: {{ $wallet->id }}</li>
    </ul>

    <p>You can view your updated wallet balance by logging into your account.</p>

    <a href="{{ route('wallet.index') }}" class="button">View Wallet</a>

    <p>Thank you for using our service.</p>

    <p>Best regards,<br>
        {{ config('app.name') }}</p>
@endsection
