@extends('layouts.email')

@section('content')
    <h1>Verify Your Email Address</h1>

    <p>Hello {{ $name }},</p>

    <p>Thank you for registering with {{ config('app.name') }}. Please click the button below to verify your email address
        and activate your account.</p>

    <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>

    <p>If you did not create an account, no further action is required.</p>

    <p>If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web
        browser:</p>

    <p style="word-break: break-all;">{{ $verificationUrl }}</p>

    <p>This verification link will expire in 60 minutes.</p>

    <p>Best regards,<br>
        {{ config('app.name') }}</p>
@endsection
