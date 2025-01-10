<!DOCTYPE html>
<html lang="en" 
    >
{{-- {{$page == "newsletters" ? "active-nav-link" : ""}} --}}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

    <title>Billing - Admin Dashboard</title>


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
            width: 120px;
            height: 30px;
        }

        .active-nav-link {
            background: #6E6EAD;
        }

        /* Update sidebar colors to support dark mode */
        .bg-sidebar { @apply bg-indigo-600 dark:bg-gray-800; }
        
        .active-nav-link { @apply bg-indigo-800 dark:bg-gray-700; }
        
        .nav-item:hover { @apply bg-indigo-800 dark:bg-gray-700; }
        
        .account-link:hover { @apply bg-indigo-800 dark:bg-gray-700; }

        /* Your existing scrollbar styles... */
    </style>

    <!-- Add Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Initialize Alpine.js store -->
    <script>
        // Initialize dark mode before Alpine loads
        if (typeof window.Alpine === 'undefined') {
            window.Alpine = {
                store(name, value) {
                    if (!window._alpine_stores) window._alpine_stores = {};
                    window._alpine_stores[name] = value;
                }
            };
        }

        // Set initial dark mode value
        window.Alpine.store('darkMode', {
            on: localStorage.getItem('darkMode') === 'true',
            toggle() {
                this.on = !this.on;
                localStorage.setItem('darkMode', this.on);
            }
        });
    </script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body x-data="{ darkMode: $store.darkMode }"
    :class="{ 'dark': $store.darkMode?.on }"
class="font-family-karla flex bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">

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

    <aside class="bg-sidebar overflow-y-auto relative h-screen w-64 hidden sm:block dark:bg-gray-800 transition-colors duration-200">
        <div class="p-6 bg-sidebar-top">
            <a href="/dashboard" class="flex justify-center">
                <img class="billings-icon" src="{{ asset('images/billingsIcon.png') }}" alt="">
            </a>
        </div>
        <nav class="text-black text-base font-semibold ">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center {{ $page == 'dashboard' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img class="w-8 h-8" src="{{ $page == 'dashboard' ? asset('images/dashboardIcon.png') : asset('images/dashboardIconInactive.png') }}" alt="">
                </span>
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center {{ $page == 'users' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img class="w-8 h-8" src="{{ $page == 'users' ? asset('images/userIcon.png') : asset('images/userIconInactive.png') }}" alt="">
                </span>
                Users
            </a>
            <a href="{{ route('admin.wallets.index') }}"
                class="flex items-center {{ $page == 'wallet' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img class="w-8 h-8" src="{{ $page == 'wallet' ? asset('images/walletIcon.png') : asset('images/walletIconInactive.png') }}" alt="">
                </span>
                Wallet
            </a>
            <a href="{{ route('admin.adaccounts.index') }}"
                class="flex items-center {{ $page == 'adaccounts' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img class="w-8 h-8" src="{{ $page == 'adaccounts' ? asset('images/adaccountIcon.png') : asset('images/adaccountIconInactive.png') }}" alt="">
                </span>
                Ad Accounts
            </a>
            <a href="{{ route('admin.rockads.accounts.index') }}"
                class="flex items-center {{ $page == 'rockads' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img class="w-8 h-8" src="{{ $page == 'rockads' ? asset('/images/rockAdaccountIcon.png') : asset('/images/rockAdaccountIconInactive.png') }}" alt="">
                </span>
                RockAds Accounts
            </a>
            <a href="{{ route('admin.meta.accounts.index') }}"
                class="flex items-center {{ $page == 'meta-accounts' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img class="w-8 h-8" src="{{ $page == 'meta-accounts' ? asset('/images/metaAccountIcon.png') : asset('/images/metaAccountIconInactive.png') }}" alt="">
                </span>
                Meta Accounts
            </a>
            <a href="{{ route('admin.business-managers.index') }}"
                class="flex items-center {{ $page == 'business-managers' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img class="w-8 h-8" src="{{ $page == 'business-managers' ? asset('/images/businessManagerIcon.png') : asset('/images/businessManagerIconInactive.png') }}" alt="">
                </span>
                Business Managers
            </a>
            <a href="{{ route('admin.adminsettings.index') }}"
                class="flex items-center {{ $page == 'settings' ? 'active-nav-link text-white' : 'text-black' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <img class="w-8 h-8" src="{{ $page == 'settings' ? asset('images/settingsIcon.png') : asset('images/settingsIconInactive.png') }}" alt="">
                </span>
                Settings
            </a>
        </nav>
        <!-- <a href="/user/logout"
            class="absolute w-full upgrade-btn bottom-0 active-nav-link flex items-center justify-center py-4">
            <i class="fas fa-arrow-circle-right mr-3"></i>
            Logout
        </a> -->
    </aside>

    <div class="w-full flex flex-col h-screen overflow-y-hidden bg-white dark:bg-gray-900">
        <!-- Desktop Header -->
        <header class="w-full items-center bg-white py-4 px-6 hidden sm:flex">
            <div x-data="{ isOpen: false }" class="relative w-full flex justify-end items-center">
                <!-- Dark Mode Toggle -->
                <button id="dark-mode-toggle" class="mr-4 p-2 rounded-lg text-gray-600 hover:bg-gray-200">
                    <!-- Sun Icon -->
                    <svg class="w-6 h-6 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <!-- Moon Icon -->
                    <svg class="w-6 h-6 moon-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                </button>

                <!-- Profile Dropdown -->
                <button @click="isOpen = !isOpen"
                    class="realtive z-10 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none">
                    <img src="https://ui-avatars.com/api/?color=6c5ce7&background=fff&name=AD" />
                </button>
                <button x-show="isOpen" @click="isOpen = false"
                    class="h-full w-full fixed inset-0 cursor-default"></button>
                <div x-show="isOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16">
                    <a href="#" class="block px-4 py-2 account-link hover:text-white">Account</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 account-link hover:text-white">
                            Logout
                        </button>
                    </form>
                </div>
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
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center {{ $page == 'dashboard' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i></i>
                    Dashboard
                </a>
                <a href="blank.html"
                    class="flex items-center {{ $page == 'users' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-sticky-note mr-3"></i>
                    Users
                </a>
                <a href="/wallet"
                    class="flex items-center {{ $page == 'wallet' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-table mr-3"></i>
                    Wallet
                </a>
                <a href="{{ route('admin.adaccounts.index') }}"
                    class="flex items-center {{ $page == 'adaccounts' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-ad mr-3"></i>
                    Ad Accounts
                </a>
                <a href="{{ route('admin.rockads.accounts.index') }}"
                    class="flex items-center {{ $page == 'rockads' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-ad mr-3"></i>
                    RockAds Accounts
                </a>
                <a href="{{ route('admin.meta.accounts.index') }}"
                    class="flex items-center {{ $page == 'meta-accounts' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-ad mr-3"></i>
                    Meta Accounts
                </a>
                <a href="{{ route('admin.business-managers.index') }}"
                    class="flex items-center {{ $page == 'business-managers' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
                    <i class="fas fa-users mr-3"></i>
                    Business Managers
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" class="block">
                    @csrf
                    <button type="submit" class="flex items-center opacity-75 hover:opacity-100 py-2 pl-4 nav-item w-full">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Sign Out
                    </button>
                </form>
            </nav>
        </header>

        {{ $slot }}
        <div class="flex-grow"></div>
        <footer class="flex bg-white justify-between items-center w-full max-w-screen bg-white dark:bg-gray-800 text-right p-4">
            <p>Billing is developed by <a href="https://bloomdigitmedia.com" class="underline text-black dark:text-white">BLOOM
                    DIGITAL MEDIA LTD.</a> 2024. All Rights Reserved</p>
            <div class="flex">
                <a href="https://www.instagram.com/bloom_digitalmedia?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                    target="_blank"><img src="/images/instagram.png" alt="Instagram Link" /></a>
                <a href="https://x.com/bloomdigitmedia?s=20" target="_blank"><img src="/images/twitter.png"
                        alt="X Link" /></a>
                <a href="https://www.facebook.com/bloomdigitmedia/" target="_blank"><img
                        src="/images/facebook.png" alt="Facebook Link" /></a>
                <a href="https://www.linkedin.com/company/bloom-digital-media-nigeria/" target="_blank"><img
                        src="/images/linkedin.png" alt="LinkedIn Link" /></a>
            </div>
        </footer>
    </div>

 
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"
        integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- Add this script after your header -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const darkModeToggle = document.getElementById('dark-mode-toggle');
            const sunIcon = darkModeToggle.querySelector('.sun-icon');
            const moonIcon = darkModeToggle.querySelector('.moon-icon');
            
            const checkTheme = () => {
                const userTheme = localStorage.getItem('theme');
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                if (userTheme === 'dark' || (!userTheme && systemTheme)) {
                    document.documentElement.classList.add('dark');
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                } else {
                    document.documentElement.classList.remove('dark');
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                }
            };

            checkTheme();

            darkModeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                
                // Toggle icons
                sunIcon.classList.toggle('hidden');
                moonIcon.classList.toggle('hidden');
            });
        });
    </script>

</body>

</html>