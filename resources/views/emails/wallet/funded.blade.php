@component('mail::message')
# Wallet Funded

Dear {{ $wallet->organization->users->first()->name }},

Your wallet has been credited with {{ $wallet->currency }} {{ number_format($amount, 2) }} by the administrator.

**Transaction Details:**
- Amount: {{ $wallet->currency }} {{ number_format($amount, 2) }}
- Description: {{ $description }}
- Wallet ID: {{ $wallet->id }}

You can view your updated wallet balance by logging into your account.

@component('mail::button', ['url' => route('wallet.index')])
View Wallet
@endcomponent

Thank you for using our service.

Best regards,<br>
{{ config('app.name') }}
@endcomponent
