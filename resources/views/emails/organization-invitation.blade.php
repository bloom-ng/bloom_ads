@extends('layouts.email')

@section('content')
    <h1>Hello!</h1>

    <p>You have been invited to join {{ $organizationName }} on {{ config('app.name') }}.</p>

    <p>You have been assigned the role of {{ $role }}.</p>

    <a href="{{ $url }}" class="button">Accept Invitation</a>

    <p>This invitation will expire in 7 days.</p>

    <p>If you did not expect this invitation, you can ignore this email.</p>

    <p>Regards,<br>{{ config('app.name') }}</p>
@endsection
