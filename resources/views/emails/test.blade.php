@extends('layouts.email')

@section('content')
    <h1>Hello!</h1>

    <p>This is a test email from {{ config('app.name') }} to verify that the email system is working correctly.</p>

    <p>If you received this email, it means your email configuration is working properly.</p>

    <p>Regards,<br>{{ config('app.name') }}</p>
@endsection
