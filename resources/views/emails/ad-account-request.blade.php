@extends('layouts.email')

@section('content')
    <h1>New Ad Account Request</h1>
    <p>A new ad account request has been submitted by {{ $userName }}.</p>

    <h2>Account Details:</h2>
    <ul>
        <li>Account Name: {{ $adAccountDetails['account_name'] }}</li>
        <li>Account Type: {{ $adAccountDetails['account_type'] }}</li>
        <li>Timezone: {{ $adAccountDetails['timezone'] }}</li>
        <li>Currency: {{ $adAccountDetails['currency'] }}</li>
        <li>Business Manager ID: {{ $adAccountDetails['business_manager_id'] }}</li>
        <li>Landing Page: {{ $adAccountDetails['landing_page'] }}</li>
        <li>Facebook Page URL: {{ $adAccountDetails['facebook_page_url'] }}</li>
    </ul>
@endsection
