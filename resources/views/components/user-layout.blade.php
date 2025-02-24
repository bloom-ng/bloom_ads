@php
    use Illuminate\Support\Facades\Auth;
@endphp

<!DOCTYPE html>
<html lang="en" class="{{ auth()->user()->dark_mode ? 'dark' : '' }}">
{{-- {{$page == "newsletters" ? "active-nav-link" : ""}} --}}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <link rel="icon" href="{{ asset('/images/fav-icon.png') }}" type="image/png">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <title>Billiing - User Dashboard</title>


    <style>
        @import url('https://fonts.googleapis.com/css?family=Karla:400,700&display=swap');

        /* Disable dark mode on mobile */
        @media (max-width: 640px) {
            html.dark {
                /* Override dark mode styles */
                background-color: white !important;
                color: black !important;
            }
            html.dark * {
                /* Force light mode colors for all elements */
                background-color: inherit !important;
                color: inherit !important;
            }
        }

        .font-family-karla {
            font-family: karla;
        }

        .footer {
            background: #FFFFFF;
            color: black;
        }

        .dark .footer {
            background: #000013;
            color: white;
        }

        .table-header {
            background: #F7F7F7;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .dark .table-header {
            background: #000013;
            color: #e5e7eb;
            border-top: 1px solid #6b7280;
        }

        .table-body {
            background: #FFFFFF;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        .dark .table-body {
            background: #000013;
            color: #d1d5db;
            border-top: 1px solid #e5e7eb;
        }

        button {
            border: 1px solid #000080;
            color: #000080;            
        }

        .dark button {
            border: 1px solid #FFFFFF;
            color: #FFFFFF;    
        }

        .bg-sidebar {
            background: #F0F0F0;
        }

        .dark .bg-sidebar {
            background: #000019;
        }       

        .dark .active-nav-link {
            background: #CECEFF;
            opacity: 95%;
            color: black;
        }

        .dark .dark-hidden {
            display: none;            
        }

        .dark .dark-block {
            display: block;
        }
        
        /* Ensures elements with dark-block are hidden in light mode */
        .dark-block {
            display: none; /* Default behavior in light mode */
        }

        .dark .active-nav-link:hover {
            opacity: 100%;
        }

        .header {
            background: #F0F0F0;
        }

        .dark .header {
            background: #000013;
        }

        .bg-sidebar-top {
            background: #000031;
        }

        .billings-icon {
            width: 130px;
            height: 35px;
        }

        .dark .bg-sidebar-top {
            background: #000013;
        }

        .billings-icon {
            width: 120px;
            height: 30px;
        }

        .active-nav-link {
            background: #6E6EAD;
            color: white;
            opacity: 75%;
        }

        .inactive-nav-link {
            color: black; /* Black text for light mode inactive links */
        }

        /* For inactive nav link in dark mode */
        .dark .inactive-nav-link {
            color: white; /* White text for dark mode inactive links */
        }        

        .inverse-text {
            color: #f0f0f0;
        }

        .dark .inverse-text {
            color: #000000;
        }

        .text {
            color: #000000;
        }

        .dark .text {
            color: #f0f0f0;
        }

        .dark .bg-body {
            background: #000013;
        }

        .dark #darklink {
            color: #F0F0F0;
        }

        .btn{
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

        .dark .btn-primary{
            border-radius: 0.75rem;
            border: 1px solid #000080;
            font-size: 18px;
            color: #000080;
        }

        .nav-item:hover {
            opacity: 100%;
        }

        .card {
            background: #F1F1FF;
            border-radius: 20px;
        }

        .dark .card {
            background: #F1F1FF;
            color: #000000;
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
        
        .btn {
            background: #000080;
            border-radius: 0.75rem;
            font-size: 18px;
        }        

        .dark #darklink {
            color: white;
        }

        .dashboard {
            background: #FFFFFF;
        }

        .dark .dashboard {
            background: #000013;
            color: #FFFFFF;
        }

        .forms{
            box-shadow: 0 0 4px 0 rgba(0,0,0,0.1);
            appearance: none;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
            color: #4a5568;
            line-height: 1.25;
            outline: none;
            transition: all 0.15s ease-in-out;
            
        }
    </style>

    <!-- Add Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // Check if device is mobile
        const isMobile = window.matchMedia('(max-width: 640px)').matches;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Remove dark mode class on mobile
            if (isMobile) {
                document.documentElement.classList.remove('dark');
            }
            
            // Initialize dark mode from user preference
            document.documentElement.classList.toggle('dark', {{ auth()->user()->dark_mode ? 'true' : 'false' }});
        });
    </script>
</head>

<body class="dashboard font-family-karla flex transition-colors duration-200">
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

    <aside class="bg-sidebar overflow-y-auto relative h-screen w-64 hidden sm:block transition-colors duration-200">
        <div class="p-6 bg-sidebar-top">
            <a href="/dashboard" class="flex justify-center">
                <img class="billings-icon" src="{{ asset('images/billingsIcon.png') }}" alt="Billing Logo">
            </a>
            @php
                use App\Models\Organization;

                $currentOrganizationId = auth()->user()->settings->current_organization_id ?? null;
                $currentOrganization = $currentOrganizationId ? Organization::find($currentOrganizationId) : null;
            @endphp

            @if ($currentOrganization)
                <p class="text-sm text-white text-center mt-2">Current Business: {{ $currentOrganization->name }}
                </p>
            @else
                <p class="text-sm text-white text-center mt-2">No business selected</p>
            @endif
        </div>
        <nav class="text-base font-semibold">
            <a href="/dashboard"
                class="flex items-center {{ $page == 'dashboard' ? 'active-nav-link' : 'inactive-nav-link' }} 
                    opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <!-- Image switching based on active class and dark mode -->
                    @if($page == 'dashboard')
                        <!-- Active State -->
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/dashboardIcon.png') }}" 
                            alt="Active Dashboard Light Mode">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/darkDashboardIcon.png') }}" 
                            alt="Active Dashboard Dark Mode">
                    @else
                        <!-- Inactive State -->
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/dashboardIconInactive.png') }}" 
                            alt="Inactive Dashboard Light Mode">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/dashboardIcon.png') }}" 
                            alt="Inactive Dashboard Dark Mode">
                    @endif
                </span>
                <span>
                    Dashboard
                </span>
            </a>
            <a href="/wallet"
                class="flex items-center {{ $page == 'wallet' ? 'active-nav-link' : 'inactive-nav-link' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'wallet')
                        <!-- active state -->
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/walletIcon.png') }}" 
                            alt="Active Dashboard Light Mode">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/walleticonblack.png') }}" 
                            alt="Active Dashboard Dark Mode">
                    @else
                        <!-- Inactive State -->
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/walletIconInactive.png') }}" 
                            alt="Inactive Dashboard Light Mode">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/walletIcon.png') }}" 
                            alt="Inactive Dashboard Dark Mode">
                    @endif
                </span>
                Wallet
            </a>
            <a href="/dashboard/adaccounts"
            class="flex items-center {{ $page == 'adaccounts' ? 'active-nav-link' : 'inactive-nav-link' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'adaccounts')
                        <!-- active state -->
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/adaccountIcon.png') }}" 
                            alt="Active Dashboard Light Mode">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/darkadaccountIcon.png') }}" 
                            alt="Active Dashboard Dark Mode">
                    @else
                        <!-- Inactive State -->
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/adaccountIconInactive.png') }}" 
                            alt="Inactive Dashboard Light Mode">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/adaccountIcon.png') }}" 
                            alt="Inactive Dashboard Dark Mode">
                    @endif
                </span>
                <p class="pl-2">Ad Accounts</p>
            </a>
            <a href="{{ route('organizations.index') }}"
                class="flex items-center {{ $page == 'organizations' ? 'active-nav-link' : 'inactive-nav-link' }} 
                opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'organizations')
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/organizationIcon.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/darkUserIcon.png') }}" alt="">
                    @else
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/organizationIconInactive.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/organizationIcon.png') }}" alt="">
                    @endif
                </span>
                <p class="pl-2">Business</p>
            </a>
            <a href="{{ route('settings.index') }}"
            class="flex items-center {{ $page == 'settings' ? 'active-nav-link' : 'inactive-nav-link' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'settings')
                        <!-- active state -->
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/settingsIcon.png') }}" 
                            alt="Active Dashboard Light Mode">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/darksettingsIcon.png') }}" 
                            alt="Active Dashboard Dark Mode">
                    @else
                        <!-- Inactive State -->
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/settingsIconInactive.png') }}" 
                            alt="Inactive Dashboard Light Mode">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/settingsIcon.png') }}" 
                            alt="Inactive Dashboard Dark Mode">
                    @endif
                </span>
                <p class="pl-2">Settings</p>
            </a>
        </nav>
    </aside>

    <div class="w-full flex flex-col h-screen overflow-y-hidden">
        <!-- Desktop Header -->
        <header class="w-full items-center bg-white py-4 px-6 header hidden sm:flex">
            <div x-data="{ isOpen: false }" class="relative w-full flex justify-end items-center">
                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="mr-4 p-2 rounded-lg text-gray-600 hover:bg-gray-200">
                    <!-- Sun Icon -->
                    <svg id="sunIcon" class="w-6 h-6 dark:hidden" fill="none" stroke="#FFFFFF" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <!-- Moon Icon -->
                    <svg id="moonIcon" class="w-6 h-6 hidden dark:block" fill="none" stroke="#000000" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                </button>
                <script>
                    document.querySelector('#darkModeToggle').addEventListener('click', function() {
                        let darkMode = document.documentElement.classList.contains('dark') ? 0 : 1;

                        fetch('/update-dark-mode', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                dark_mode: darkMode
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Apply the dark class based on the toggle state
                                document.documentElement.classList.toggle('dark', darkMode === 1);
                                // Toggle icon visibility
                                document.querySelector('#sunIcon').classList.toggle('hidden');
                                document.querySelector('#moonIcon').classList.toggle('hidden');
                            }
                        })
                        .catch(error => console.log(error));
                    });
                </script>
                <button @click="isOpen = !isOpen"
                    class="realtive z-10 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none">
                    <img src="https://ui-avatars.com/api/?color=6c5ce7&background=fff&name={{ auth()->user()->name }}" />
                </button>
                <button x-show="isOpen" @click="isOpen = false"
                    class="h-full w-full fixed inset-0 cursor-default"></button>
                <div x-show="isOpen" class="absolute w-32 bg-white rounded-lg bg-sidebar shadow-lg py-2 mt-16">
                    <a href="{{ route('account') }}" class="block px-4 py-2 account-link dark:hover:opacity-50">Account</a>
                    <form method="POST" action="{{ route('user.logout') }}" class="block">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 account-link dark:hover:opacity-50">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex text-gray-800 items-center">
                <x-notification-dropdown />
                <!-- Other header items -->
            </div>
        </header>

        <!-- Mobile Header & Nav -->
        <header x-data="{ isOpen: false }" class="w-full bg-sidebar py-5 px-6 sm:hidden">
            <div class="flex items-center justify-between">
                <a href="/dashboard" class="w-24 md:w-34">
                    <img src="{{ asset('images/billings2 1.png') }}" alt="Billing Logo">
                </a>
                <button @click="isOpen = !isOpen" class="text-[#000080] text-3xl focus:outline-none">
                    <i x-show="!isOpen" class="fas fa-bars"></i>
                    <i x-show="isOpen" class="fas fa-times"></i>
                </button>
            </div>

            <!-- Dropdown Nav -->
            <nav x-show="isOpen" class="flex flex-col pt-4">
                <a href="/dashboard"
                    class="flex items-center {{ $page == 'dashboard' ? 'active-nav-link text-white font-semibold' : 'text-black font-medium' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <img src="{{ $page == 'dashboard' ? asset('images/dashboardIcon.png') : asset('images/dashboardIconInactive.png') }}"
                        alt="Dashboard" class="w-4 h-4 mr-4">
                    Dashboard
                </a>
                <a href="/wallet"
                    class="flex items-center {{ $page == 'wallet' ? 'active-nav-link text-white font-semibold' : 'text-black font-medium' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <img src="{{ $page == 'wallet' ? asset('images/walletIcon.png') : asset('images/walletIconInactive.png') }}"
                        alt="Wallet" class="w-4 h-4 mr-4">
                    Wallet
                </a>
                <a href="/dashboard/adaccounts"
                    class="flex items-center {{ $page == 'adaccounts' ? 'active-nav-link text-white font-semibold' : 'text-black font-medium' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <img src="{{ $page == 'adaccounts' ? asset('images/adaccountIcon.png') : asset('images/adaccountIconInactive.png') }}"
                        alt="Ad Accounts" class="w-4 h-4 mr-4">
                    Ad Account
                </a>
                <a href="{{ route('organizations.index') }}"
                    class="flex items-center {{ $page == 'organizations' ? 'active-nav-link text-white font-semibold' : 'text-black font-medium' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <img src="{{ $page == 'organizations' ? asset('images/organizationIcon.png') : asset('images/organizationIconInactive.png') }}"
                        alt="Organizations" class="w-4 h-4 mr-4">
                    Business
                </a>
                <a href="{{ route('settings.index') }}"
                    class="flex items-center {{ $page == 'settings' ? 'active-nav-link text-white font-semibold' : 'text-black font-medium' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <img src="{{ $page == 'settings' ? asset('images/settingsIcon.png') : asset('images/settingsIconInactive.png') }}"
                        alt="Settings" class="w-4 h-4 mr-4">
                    Settings
                </a>
                <form method="POST" action="{{ route('user.logout') }}" class="block">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center opacity-75 hover:opacity-100 py-2 pl-4 nav-item text-black">
                        <i class="fas fa-sign-out-alt mr-3 text-[#000080]"></i>
                        Sign Out
                    </button>
                </form>
            </nav>
        </header>

        {{ $slot }}
        <div class="flex-grow"></div>
        <footer class="flex bg-white footer justify-between items-center w-full max-w-screen text-right p-4">
            <p>Billing is a product of <a href="https://bloomdigitmedia.com" class="underline text text-black">BLOOM
                    DIGITAL MEDIA LTD.</a> {{ date('Y') }}. All Rights Reserved</p>
            <div class="flex">
                <a class="dark-hidden" href="https://www.instagram.com/bloom_digitalmedia?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank">
                    <img src="/images/instagram.png" alt="Instagram Link" />
                </a>
                <a class="dark-block" href="https://www.instagram.com/bloom_digitalmedia?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank">
                    <img src="/images/darkInstagram.png" alt="Instagram Link" />
                </a>

                <a class="dark-hidden" href="https://x.com/bloomdigitmedia?s=20" target="_blank">
                    <img src="/images/twitter.png" alt="X Link" />
                </a>
                <a class="dark-block" href="https://x.com/bloomdigitmedia?s=20" target="_blank">
                    <img src="/images/darkTwitter.png" alt="X Link" />
                </a>

                <a class="dark-hidden" href="https://www.facebook.com/bloomdigitmedia/" target="_blank">
                    <img src="/images/facebook.png" alt="Facebook Link" />
                </a>
                <a class="dark-block" href="https://www.facebook.com/bloomdigitmedia/" target="_blank">
                    <img src="/images/darkFacebook.png" alt="Facebook Link" />
                </a>

                <a class="dark-hidden" href="https://www.linkedin.com/company/bloom-digital-media-nigeria/" target="_blank">
                    <img src="/images/linkedin.png" alt="LinkedIn Link" />
                </a>
                <a class="dark-block" href="https://www.linkedin.com/company/bloom-digital-media-nigeria/" target="_blank">
                    <img src="/images/darkLinkedin.png" alt="LinkedIn Link" />
                </a>
            </div>
        </footer>
    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</body>

</html>
