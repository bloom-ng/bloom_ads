<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-bind:class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing | The Premier Platform for Google and Meta Ads in Nigeria</title>
    <link rel="icon" href="{{ asset('/images/fav-icon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://unpkg.com/alpinejs" defer></script>

    <meta name="description"
        content="Effortlessly manage your Google, Meta and TikTok ads in Nigeria with our premier platform, designed to streamline your billing process.">

    <!-- Facebook Meta Tags -->
    <meta property="og:url" content="https://billing.ad">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Billing | The Premier Platform for Google and Meta Ads in Nigeria">
    <meta property="og:description"
        content="Effortlessly manage your Google, Meta and TikTok ads in Nigeria with our premier platform, designed to streamline your billing process.">
    <meta property="og:image" content="{{ asset('/images/graph_image.png') }}">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="billing.ad">
    <meta property="twitter:url" content="https://billing.ad">
    <meta name="twitter:title" content="Billing | The Premier Platform for Google and Meta Ads in Nigeria">
    <meta name="twitter:description"
        content="Effortlessly manage your Google, Meta and TikTok ads in Nigeria with our premier platform, designed to streamline your billing process.">
    <meta name="twitter:image" content="{{ asset('/images/graph_image.png') }}">

    <!-- Meta Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1070616598420924');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=1070616598420924&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->
</head>

<body class="h-full w-full">
    <!-- Add theme toggle to your navigation -->
    {{-- <x-theme-toggle /> --}}

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

    <!-- desktop header -->
    <header class="md:flex flex-row w-full justify-between items-center bg-[#000031] py-8 px-16 hidden">
        <a href="/" class="w-1/5">
            <img src="/images/billingsIcon.png" alt="" class="w-[107px] h-[35px]">
        </a>

        @if (!$isAuth)
            <div class="flex-row items-center space-x-5">
                <a href="/login"
                    class="rounded-xl border-[#FFFFFF] border-2 text-white font-semibold text-base p-2 px-10">Login</a>
                <a href="/signup" class="rounded-xl bg-[#FFFFFF] text-[#000031] font-semibold text-base p-2 px-7">Get
                    Started</a>
            </div>
        @endif
    </header>

    <!-- mobile header -->
    <header class="flex flex-row w-full bg-[#000031] justify-between items-center px-8 py-5 md:hidden">
        <a href="/"><img src="/images/billingsIcon.png" alt="" class="w-[107px] h-[35px]"></a>

        <!-- <button id="menu-btn" class="text-gray-700 focus:outline-none">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 12h18M3 6h18M3 18h18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button> -->

        <a href="/login" class="rounded-xl bg-[#FFFFFF] text-[00031] font-semibold text-base p-2 px-7">
            Login</a>

    </header>
    <main>
        {{ $slot }}
    </main>

    <footer
        class="bg-[#000031] flex flex-col w-full justify-start items-start md:justify-center md:items-center md:py-14 md:px-0 px-10">
        @if (!$isAuth)
            <p class="md:text-4xl text-2xl text-white mb-5 md:mb-10 mt-5">Billing Financial Solutions</p>

            <div class="grid grid-cols-1 mb-5 md:mb-10 md:px-14">
                <img src="/images/flutterwave.png" alt="interswitch logo" class="h-[60%] w-[80%]">
                {{-- <img src="/images/paypal-logo.png" alt="paypal logo" class="h-[60%] w-[80%]"> --}}
            </div>
        @endif


        <div class="flex flex-col md:flex-row md:space-x-10 mb-10 text-white text-[14px] lg:text-3xl">
            <a href="/service">Service Agreement</a>
            <a href="/purchase">Purchase Policy</a>
            <a href="/privacy">Privacy Policy</a>
            <a href="/contact">Contact Us</a>
        </div>

        <div class="flex flex-row lg:space-x-3 mb-10">
            <span>
                {{-- <a class="dark-hidden"
                    href="https://www.instagram.com/billing.ads?igsh=MTdjZWN4cXhyaDVkeg=="
                    target="_blank"><img src="/images/instagram.png" alt="Instagram Link" /></a> --}}
                <a class="dark-block" href="https://www.instagram.com/billing.ads?igsh=MTdjZWN4cXhyaDVkeg=="
                    target="_blank"><img src="/images/darkModeInstagram.png" alt="Instagram Link" /></a>
            </span>
            <span>
                {{-- <a class="dark-hidden" href="https://x.com/billing_ad?s=21" target="_blank"><img
                        src="/images/twitter.png" alt="X Link" /></a> --}}
                <a class="dark-block" href="https://x.com/billing_ad?s=21" target="_blank"><img
                        src="/images/darkModeX.png" alt="X Link" /></a>
            </span>
            <span>
                {{-- <a class="dark-hidden" href="https://www.facebook.com/share/1Z1b3CEDeG/?mibextid=wwXIfr" target="_blank"><img
                        src="/images/facebook.png" alt="Facebook Link" /></a> --}}
                <a class="dark-block" href="https://www.facebook.com/share/1Z1b3CEDeG/?mibextid=wwXIfr"
                    target="_blank"><img src="/images/darkModeFacebook.png" alt="Facebook Link" /></a>
            </span>
            <span>
                {{-- <a class="dark-hidden" href="https://www.linkedin.com/company/bloom-digital-media-nigeria/"
                    target="_blank"><img src="/images/linkedin.png" alt="LinkedIn Link" /></a> --}}
                <a class="dark-block" href="https://www.linkedin.com/company/bloom-digital-media-nigeria/"
                    target="_blank"><img src="/images/darkModeLinkedin.png" alt="LinkedIn Link" /></a>
            </span>
        </div>

        <div class="flex flex-row mb-5 space-x-2">
            <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="mt-1a">
                <rect width="25" height="25" fill="url(#pattern0_561_390)" />
                <defs>
                    <pattern id="pattern0_561_390" patternContentUnits="objectBoundingBox" width="1"
                        height="1">
                        <use xlink:href="#image0_561_390" transform="scale(0.0111111)" />
                    </pattern>
                    <image id="image0_561_390" width="90" height="90"
                        xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFcElEQVR4nO2da4jUVRTAr2VZWFaSlCSUWJShiVYfpIcSPSCKXmTgFqHVkihtZu+nINGDCNkIAqkPBfUhen9QE5QiLBZjfUIQayWZ2bKbPd0l7ReHPZINzcz9z5z7f94fzJdh5pxzz8zcxznnnnEuEolEIpFIJBKJRCJ+AEcCFwJ3AM8D7wNbgT5gEBjWx6A+t1Vf85y+5wKR4amuWgCTgHuBj4BfaJ99wIdAF3CaqzLAscBtwDrgIOE4AHwM3Aoc46oCcJx+03aTPj8By4ETXFkBjgIeAAbIHrFhmdjkygRwCbCN/PEVcLkrOjInAi8Df5NfxLZuYIwrIsAZwBcUhy+BM12RkJ+j0TYtbWRbeJkrAsANwH6KyzBwi8szQGfgPXFayBjucnkEuF4PB2XhIDDP5QmZ14AhyscwcKXLA8BZBV34kiyQU7J28hjdFpWdTZnus/UwUhW6szxW5/nEZ42MdU4WAaLtpMNO4A3gUaADuFoPRDcCi4AnNPAvc2loJMkwOk1HSxQudDhzBTA1YWZmNvCa7hZCsTSsd/8d0PEBQ52/Aw9LUsAgYxMqmNUvMXU7j9YfxIOEodd6GwVcBewJYOsySzvrpZ9+DGD4mna/xQ1sPgXYYWzvD0HTYprjs+bT0Lk8SdAC3xjb3RHSYEmkWvI9MCGYwf+1/RzjqOKaUIZOChCZu7bFqWCGJhYS1W8ATxraLgG0iUnt9zFS6i4sWZtA9zjgaWBXjQzZO78OnJsgZCBFN1YsacupdYyU4hZLZnvqPc9jfpXIYaenvEcMx/Be246tMW608clrS4Lpyrf+Q/bM8z1knm64vx40LT/TWjhLHvLU+05CuXKQGu8ht8dwLOebOFkNk+JBS6YH/HCbHpGBVwzHssDS0VLVacXPwCgPnS+FWmSN5+lnLB39gaFhGzx1ftei/D91v9/oIRVK+VsQZfEyNGyVh76TKQ69lo62PL4+5aFvDsVhp6WjLcOi93jok6B+Uei3dLRlIL3TQ9/tFIeh6OgCOtpy6ujyrN+r5NRhuRgu98ywV3IxtNzeveqh7yQqur2TdL4Vn3jqlDKDVuPEfU0ekmW34t28HsF/A47w0Plii/J7PGRLpj2XR3DroNJMD50zWwxnrvCQvSqvQSXrMOljnnrfSij3D4lhe8jdYDiWWSZOPqwKyDLwv8NT78SEwaVFHjJPNKzltg38q4Fyt9qSSz31TgW+biLrL+D+DKZBu4XwMAPlWrEl6xPoHqtXi7+tkfGrTi8zPOWMAjYajmFxW05tUIRifUflphbsmKDlBpOTVnYaFwDJr+jUpPb7GipdAizZE6Q24v9tHw/sNbR9dUhjpRWDNRtD1d3VTD2fGds9P3SRY4jqzPWhSmH1PvpaY3t3B+/9EbAIfRtwtrGt04zjNIe4z9LOLArR90uqq91vtzQ+kUNRoKvSUog+1s6jjQciTUVCMiAxBJ/6j5qt23TghcD3HpvG060vC6XV6GQX8CbwuC7G1+hlIUkO3K3Pv20ckavH5lQvC6mzL47X31JCO7dUhZVp+bVevbFc3y07PcDRmTlanT0lpcuUWSG1gpNdHgDmFrzrTD2knuUKlyeA60rYGOVml0ekPU5JWv0cAO50eUZb/hR5GhnKXYufJq1/9hV04ZvrioReyvmc4rAp85Y+be6zu3N+ghTbVma+TzY8rm8hf4hNF7kywchdxaUaZsyafk02pxsgShNG0kpdeuE+bfZqNn2cqwqMpJg6tFdHyIOOyF4tOb5KtZ5vUJG0RK6SafVPuwyqrMXBSgKKDiPlZ7OAhcCzUg2kbYD6NPNy6O9BBvS5Xn2NvHaBvjf+PUgkEolEIpFIJBKJOE/+AVhJq2eDC+U0AAAAAElFTkSuQmCC" />
                </defs>
            </svg>
            <p class="text-white text-xl">Copyright {{ date('Y') }}</p>
        </div>
    </footer>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
        </svg>`;
            } else {
                passwordInput.type = 'password';
                icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>`;
            }
        }
    </script>
</body>

</html>
