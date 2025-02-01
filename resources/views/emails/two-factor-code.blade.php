@extends('layouts.email')

@section('content')
    <h1>Hello!</h1>

    <p>Your two-factor authentication code is:</p>

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0;">
        <h2 style="font-size: 32px; color: #000080; margin: 0;">{{ $code }}</h2>
    </div>

    <p>This code will expire in 10 minutes. If you did not request this code, please ignore this email.</p>

    <p style="color: #666; font-size: 14px; margin-top: 30px;">For security reasons, never share this code with anyone.</p>

    <p>Regards,<br>{{ config('app.name') }}</p>
@endsection
