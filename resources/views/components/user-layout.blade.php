<!DOCTYPE html>
<html lang="en">
{{-- {{$page == "newsletters" ? "active-nav-link" : ""}} --}}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Include Tailwind CSS -->
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    <!-- Include Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <!-- Include Mont Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <link rel="icon" href="{{ asset('/images/fav-icon.png') }}" type="image/png">

    <title>Billiing - User Dashboard</title>


    <!-- Tailwind -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');

        .font-family-karla {
            font-family: karla;
        }

        .bg-sidebar {
            background: #F0F0F0;
        }

        .bg-sidebar-top {
            background: #000031;
        }

        .billings-icon {
            width: 130px;
            height: 35px;
        }

        .active-nav-link {
            background: #6E6EAD;
        }

        .card {
            background: #F1F1FF;
            border-radius: 20px;
        }

        .card-btn {
            background: #000080;
            border-radius: 9999px;
            color: white;
            font-size: 0.75rem;
        }

        .card-btn:hover {
            background: #000080;
        }

        .btn{
            background: #000080;
            border-radius: 0.75rem;
            font-size: 18px;
            color: white;
        }

        .btn-primary{
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #000080;
            font-size: 18px;
            color: #000080;
        }

    </style>
</head>

<body class="font-family-karla flex">

    @if (session('success'))
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <script>
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "green",
                stopOnFocus: true,
                ariaLive: "polite",
                onClick: function() {}
            }).showToast();
        </script>
    @endif
    @if (session('error'))
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <script>
            Toastify({
                text: "{{ session('error') }}",
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "red",
                stopOnFocus: true,
                ariaLive: "polite",
                onClick: function() {}
            }).showToast();
        </script>
    @endif
    @if ($errors->any())
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        @foreach ($errors->all() as $error)
            <script>
                Toastify({
                    text: "{{ $error }}",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "red",
                    stopOnFocus: true,
                    ariaLive: "polite",
                    onClick: function() {}
                }).showToast();
            </script>
        @endforeach
    @endif

    <aside class="bg-sidebar relative h-screen w-64 hidden sm:block">
        <div class="p-6 bg-sidebar-top">
            <a href="/dashboard" class="flex justify-center">
                <img class="billings-icon" src="{{ asset('images/billingsIcon.png') }}" alt="">
            </a>
            @php
                use Illuminate\Support\Facades\Auth;
                use App\Models\Organization;

                $currentOrganizationId = Auth::user()->settings->current_organization_id ?? null;
                $currentOrganization = $currentOrganizationId ? Organization::find($currentOrganizationId) : null;
            @endphp

            @if ($currentOrganization)
                <p class="text-sm text-white text-center mt-2">Current Business: {{ $currentOrganization->name }}
                </p>
            @else
                <p class="text-sm text-white text-center mt-2">No business selected</p>
            @endif
        </div>
        <nav class="text-black text-base font-semibold">
            <a href="/dashboard"
                class="flex items-center {{ $page == 'dashboard' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img src="{{ $page == 'dashboard' ? asset('images/dashboardIcon.png') : asset('images/dashboardIconInactive.png') }}"
                        alt="Dashboard" class="w-10 h-10">
                </span>
                Dashboard
            </a>
            <a href="/wallet"
                class="flex items-center {{ $page == 'wallet' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img src="{{ $page == 'wallet' ? asset('images/walletIcon.png') : asset('images/walletIconInactive.png') }}"
                        alt="Wallet" class="w-10 h-10">
                </span>
                Wallet
            </a>
            <a href="/dashboard/adaccounts"
                class="flex items-center {{ $page == 'adaccounts' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <img src="{{ $page == 'adaccounts' ? asset('images/adaccountIcon.png') : asset('images/adaccountIconInactive.png') }}"
                    alt="Ad Accounts" class="w-10 h-10">
                <p class="pl-2">Ad Accounts</p>
            </a>
            <a href="{{ route('organizations.index') }}"
                class="flex items-center {{ $page == 'organizations' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <img src="{{ $page == 'organizations' ? asset('images/organizationIcon.png') : asset('images/organizationIconInactive.png') }}"
                    alt="Organizations" class="w-10 h-10">
                <p class="pl-2">Business</p>
            </a>
            <a href="{{ route('settings.index') }}"
                class="flex items-center {{ $page == 'settings' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <img src="{{ $page == 'settings' ? asset('images/settingsIcon.png') : asset('images/settingsIconInactive.png') }}"
                    alt="Settings" class="w-10 h-10">
                <p class="pl-2">Settings</p>
            </a>
        </nav>
        <!-- <a href="/user/logout"
            class="absolute w-full upgrade-btn bottom-0 active-nav-link flex items-center justify-center py-4">
            <i class="fas fa-arrow-circle-right mr-3"></i>
            Logout
        </a> -->
    </aside>

    <div class="w-full flex flex-col h-screen overflow-y-hidden">
        <!-- Desktop Header -->
        <header class="w-full items-center bg-[#F0F0F0] py-4 px-6 hidden sm:flex">
            <div x-data="{ isOpen: false }" class="relative w-full flex justify-end">
                <button @click="isOpen = !isOpen"
                    class="realtive z-10 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none">
                    <img src="https://ui-avatars.com/api/?color=6c5ce7&background=fff&name={{ Auth::user()->name }}" />
                </button>
                <button x-show="isOpen" @click="isOpen = false"
                    class="h-full w-full fixed inset-0 cursor-default"></button>
                <div x-show="isOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16">
                    <a href="{{ route('account') }}" class="block px-4 py-2 account-link hover:text-white">Account</a>
                    <form method="POST" action="{{ route('user.logout') }}" class="block">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 account-link hover:text-white">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex items-center">
                <x-notification-dropdown />
                <!-- Other header items -->
            </div>

        </header>

        <!-- Mobile Header & Nav -->
        <header x-data="{ isOpen: false }" class="w-full bg-sidebar py-5 px-6 sm:hidden">
            <div class="flex items-center justify-between">
                <a href="index.html" class="w-24 md:w-34">
                    <img src="/images/sharepadi-rebrand-02.png" alt="">
                </a>
                <button @click="isOpen = !isOpen" class="text-[#F48857] text-3xl focus:outline-none">
                    <i x-show="!isOpen" class="fas fa-bars"></i>
                    <i x-show="isOpen" class="fas fa-times"></i>
                </button>
            </div>

            <!-- Dropdown Nav -->
            <nav :class="isOpen ? 'flex' : 'hidden'" class="flex flex-col pt-4 text-[#F48857]">
                <a href="/dashboard"
                    class="flex items-center {{ $page == 'dashboard' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i></i>
                    Dashboard
                </a>
                <a href="/wallet"
                    class="flex items-center {{ $page == 'wallet' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-table mr-3"></i>
                    Wallet
                </a>
                <form method="POST" action="{{ route('user.logout') }}" class="block">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Sign Out
                    </button>
                </form>
            </nav>
        </header>

        {{ $slot }}
        <div class="flex-grow"></div>
        <footer class="flex bg-white justify-between items-center w-full max-w-screen bg-[#F0F0F0] text-right p-4">
            <p>Billing is a product of <a href="https://bloomdigitmedia.com" class="underline text-black">BLOOM
                    DIGITAL MEDIA LTD.</a> 2024. All Rights Reserved</p>
            <div class="flex">
                <a href="https://www.instagram.com/bloom_digitalmedia?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                    target="_blank"><img src="/images/instagram.png" alt="Instagram Link" /></a>
                <a href="https://x.com/bloomdigitmedia?s=20" target="_blank"><img src="/images/twitter.png"
                        alt="X Link" /></a>
                <a href="https://www.facebook.com/bloomdigitmedia/" target="_blank"><img src="/images/facebook.png"
                        alt="Facebook Link" /></a>
                <a href="https://www.linkedin.com/company/bloom-digital-media-nigeria/" target="_blank"><img
                        src="/images/linkedin.png" alt="LinkedIn Link" /></a>
            </div>
        </footer>
    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"
        integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</body>

</html>
