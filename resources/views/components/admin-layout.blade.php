@php
    use Illuminate\Support\Facades\Auth;
@endphp

<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: {{ Auth::guard('admin')->user()->dark_mode ? 'true' : 'false' }} }" :class="{ 'dark': darkMode }">
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
        </style>

   <!-- Add Alpine.js -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Initialize Alpine.js store -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Initialize Alpine.js store -->
<script>
    // Check localStorage and apply the appropriate theme on page load
    const darkModeEnabled = localStorage.getItem('darkMode') === 'true';

    // Apply the correct mode on load
    document.documentElement.classList.toggle('dark', darkModeEnabled);

    // Initialize Alpine.js store for dark mode
    document.addEventListener('alpine:init', () => {
        Alpine.store('darkMode', {
            on: darkModeEnabled,
            toggle() {
                this.on = !this.on;
                localStorage.setItem('darkMode', this.on);
                document.documentElement.classList.toggle('dark', this.on);
            }
        });
    });
</script>




    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-family-karla flex bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">

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
        <nav class="text-base font-semibold">
            <a href="{{ route('admin.dashboard') }}"
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
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center {{ $page == 'users' ? 'active-nav-link' : 'inactive-nav-link' }} 
                opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'users')
                        <img class="w-8 h-8 dark-hidden" 
                                src="{{ asset('images/userIcon.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/darkUserIcon.png') }}" alt="">
                    @else
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/userIconInactive.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/userIcon.png') }}" alt="">
                    @endif
                </span>
                Users
            </a>
            <a href="{{ route('admin.wallets.index') }}"
                class="flex items-center {{ $page == 'wallet' ? 'active-nav-link' : 'inactive-nav-link' }} 
                opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'wallet')
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/walletIcon.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/darkWalletIcon.png') }}" alt="">
                    @else
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/walletIconInactive.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/walletIcon.png') }}" alt="">
                    @endif
                </span>
                Wallet
            </a>
            <a href="{{ route('admin.organizations.index') }}"
                class="flex items-center {{ $page == 'organizations' ? 'active-nav-link' : 'inactive-nav-link' }} 
                opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'organizations')
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/organizationIcon.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/darkOrganizationIcon.png') }}" alt="">
                    @else
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/organizationIconInactive.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/organizationIcon.png') }}" alt="">
                    @endif
                </span>
                Organizations
            </a>
            <a href="{{ route('admin.adaccounts.index') }}"
                class="flex items-center {{ $page == 'adaccounts' ? 'active-nav-link' : 'inactive-nav-link' }} 
                opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'adaccounts')
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/adaccountIcon.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/darkAdaccountIcon.png') }}" alt="">
                    @else
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/adaccountIconInactive.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/adaccountIcon.png') }}" alt="">
                    @endif
                </span>
                Ad Accounts
            </a>
            <a href="{{ route('admin.rockads.accounts.index') }}"
                class="flex items-center {{ $page == 'rockads' ? 'active-nav-link' : 'inactive-nav-link' }} 
                    opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'rockads')
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/rockAdaccountIcon.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/darkRockAdaccountIcon.png') }}" alt="">
                    @else
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/rockAdaccountIconInactive.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden"  
                            src="{{ asset('images/rockAdaccountIcon.png') }}" alt="">
                    @endif
                </span>
                RockAds Accounts
            </a>
            <a href="{{ route('admin.meta.accounts.index') }}"
                class="flex items-center {{ $page == 'meta-accounts' ? 'active-nav-link' : 'inactive-nav-link' }} 
                    opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'meta-accounts')
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/metaAccountIcon.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/darkMetaAccountIcon.png') }}" alt="">
                    @else
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/metaAccountIconInactive.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/metaAccountIcon.png') }}" alt="">
                    @endif  
                </span>
                Meta Accounts
            </a>
            <a href="{{ route('admin.business-managers.index') }}"
                class="flex items-center {{ $page == 'business-managers' ? 'active-nav-link' : 'inactive-nav-link' }} 
                    opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'business-managers')
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/businessManagerIcon.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/darkBusinessManagerIcon.png') }}" alt="">
                    @else
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('/images/businessManagerIconInactive.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('/images/businessManagerIcon.png') }}" alt="">
                    @endif
                </span>
                Business Managers
            </a>
            <a href="{{ route('admin.adminsettings.index') }}"
                class="flex items-center {{ $page == 'settings' ? 'active-nav-link' : 'inactive-nav-link' }} 
                    opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    @if($page == 'settings')
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/settingsIcon.png') }}" alt="">
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/darkSettingsIcon.png') }}" alt="">
                    @else
                        <img class="w-8 h-8 dark-hidden" 
                            src="{{ asset('images/settingsIconInactive.png') }}" alt="">    
                        <img class="w-8 h-8 dark-block dark-hidden" 
                            src="{{ asset('images/settingsIcon.png') }}" alt="">
                    @endif
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

    <div class="w-full flex flex-col h-screen overflow-y-hidden  ">
        <!-- Desktop Header -->
        <header class="w-full items-center bg-white py-4 px-6 header hidden sm:flex">
            <div x-data="{ isOpen: false }" class="relative w-full flex justify-end items-center">
                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="mr-4 p-2 rounded-lg text-gray-600 hover:bg-gray-200">
                <script>
                    document.querySelector('.darkModeToggle').addEventListener('click', function() {
                        let darkMode = document.documentElement.classList.contains('dark') ? 1 : 0;

                        fetch('/admin/update-dark-mode', {
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
                            }
                        })
                        .catch(error => console.log(error));
                    });

                </script>
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

                    <script>
                        document.querySelector('#sunIcon').addEventListener('click', toggleIcons);
                        document.querySelector('#moonIcon').addEventListener('click', toggleIcons);

                        function toggleIcons() {
                            document.querySelector('#sunIcon').classList.toggle('hidden');
                            document.querySelector('#moonIcon').classList.toggle('hidden');
                        }
                    </script>
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
            <nav :class="isOpen ? 'flex' : 'hidden'" class="flex flex-col pt-4 text-[#F48857] dark:text-white">
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

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"
        integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('darkModeToggle');
        const htmlElement = document.documentElement;

        // Load user's preference from localStorage
        if (localStorage.getItem('theme') === 'dark') {
            htmlElement.classList.add('dark');
        }

        // Toggle dark mode on button click
        toggleButton.addEventListener('click', function () {
            if (htmlElement.classList.contains('dark')) {
                htmlElement.classList.remove('dark');
                localStorage.setItem('theme', 'light'); // Save preference
            } else {
                htmlElement.classList.add('dark');
                localStorage.setItem('theme', 'dark'); // Save preference
            }
        });
    });
</script>


</body>

</html>