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
    </style>
</head>

<body>
    <div class="container">
        <img src="{{ asset('images/email_banner.jpg') }}" alt="{{ config('app.name') }} Banner" style="width: 100%;">

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
