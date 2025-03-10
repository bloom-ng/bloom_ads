<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .banner {
            width: 100%;
            height: auto;
            background-color: #000080;
            padding: 40px 0;
        }

        .content {
            padding: 40px;
            color: #000031;
            background-color: #ffffff;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #000080;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            margin: 20px 0;
        }

        .footer {
            background-color: #E6E6F3;
            padding: 20px;
            text-align: center;
            color: #000031;
        }

        .social-icons {
            margin-top: 10px;
        }

        .social-icons a {
            margin: 0 10px;
            text-decoration: none;
            color: #000031;
        }

        .social-icons img {
            width: 24px;
            height: 24px;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="{{ asset('images/email_banner.jpg') }}" alt="{{ config('app.name') }} Banner" style="width: 100%;">

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            <p>Best regards,<br>
                {{ config('app.name') }}</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <div class="social-icons">
                <a href="https://www.instagram.com/billing.ads?igsh=MTdjZWN4cXhyaDVkeg==" target="_blank"><img
                        src="{{ asset('images/instagram.png') }}" alt="Billing Instagram"></a>
                <a href="https://x.com/billing_ad?s=21" target="_blank"><img src="{{ asset('images/twitter.png') }}"
                        alt="Billing Twitter"></a>
                <a href="https://www.facebook.com/share/1Z1b3CEDeG/?mibextid=wwXIfr" target="_blank"><img
                        src="{{ asset('images/facebook.png') }}" alt="Billing Facebook"></a>
            </div>
        </div>
    </div>
</body>

</html>
