@extends('layouts.email')

@section('content')
    <h1>Hello!</h1>

    <p>{{ $messages }}</p>

    @if (isset($actionUrl))
        <a href="{{ $actionUrl }}" class="button">{{ $actionText ?? 'View Details' }}</a>
    @endif

    <p>Regards,<br>{{ config('app.name') }}</p>
@endsection
