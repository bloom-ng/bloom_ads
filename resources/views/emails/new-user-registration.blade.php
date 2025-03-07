@extends('layouts.email')

@section('content')
    <h2>New User Registration Notification</h2>

    <p>A new user has registered on the platform.</p>

    <h3>User Details:</h3>
    <ul>
        <li><strong>Name:</strong> {{ $userName }}</li>
        <li><strong>Email:</strong> {{ $userEmail }}</li>
        <li><strong>Business Name:</strong> {{ $businessName }}</li>
        <li><strong>User Type:</strong> {{ $userType }}</li>
        <li><strong>Country:</strong> {{ $country }}</li>
    </ul>

    <p>Please review this registration at your earliest convenience.</p>
@endsection
