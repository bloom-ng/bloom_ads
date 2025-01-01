<!DOCTYPE html>
<html lang="en" 
    x-data 
    :class="{ 'dark': $store.darkMode.on }">
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
            background: #E6E6F3;
        }

        .active-nav-link {
            background: #E6E6F3;
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
        <div class="p-6 bg-[#F48857]">
            <a href="/admin/dashboard" class="">
                <h1 class="text-md lg:text-2xl font-semibold text-center">Billing</h1>
            </a>
        </div>
        <nav class="text-black text-base font-semibold pt-3">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center {{ $page == 'dashboard' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <svg width="52" height="52" viewBox="0 0 52 52" fill="none"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="52" height="52" fill="url(#pattern0_563_556)"/>
                        <defs>
                            <pattern id="pattern0_563_556" patternContentUnits="objectBoundingBox" width="1"
                                height="1">
                                <use xlink:href="#image0_563_556" transform="scale(0.0104167)" />
                            </pattern>
                            <image id="image0_563_556" width="96" height="96"
                                xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAAAsTAAALEwEAmpwYAAACoElEQVR4nO2cW24TQRBFvZWIwCogHxCW4O6woXSZLIDnVgJhERD2kIc8UyX506ixCD8GhFIzfTt1j9S/0a0+025bVZnFghBCCCGEEEIIIYSQ/0Dl5KVK/mAlf9eSzCRv77P0599Il1ryey3p+KHlcmM4zU9M8pf7Fmb/WiVfDKtXj3vP5cpY0pFJup28SLkr9mZc5We95nJl94TNWKT8LnZ9ujzsLZc7VvKn2YuUu2IvesvlSr3YmhUpu6Wr/KKXXP4CSvrYvFBJ73rJ5U79Ste6UJN02UsufwGStX2hWXvJNYWALcJadJKLAoQCeAI8aX3EjR9B7TffeAdQgDW8hAG+7qUhrgCEHzwlfQsroDYjWhepkt8EFpCOWxep5eR5WAG7YtN5uyLT5/2ZAgnYnC0PrKQrpMaHRRJQqW24uiEzFnc9yvLpn/KEE1CpT+M8Xah0vn6dH/0tS0gBv6idoNqMqF8PnX4nqEn+qiW/3Xfh7iO0AAQMYPMpQCiAJyAqFvoSBpjBtIgCkGYwLZoAtBlMiyQAcQbTIglAnMG0KAJQZzAtjADQGUyLIgB4NnRsnqvkcQ4BkIUaaK/aX0DrImW3eulVBxKQIHvVYQSg9qrdQRawAexVhxKA2Kt2B10AWq/anR4EIPWq3elJwIOk9cYbBbTffOMJoIBm9HQCFKBX7U4PAgagXrU76AJGsF51KAEDYK86lAAD7FWHEaCgveo4AgpmrzqMAAv0viBMAYLwD+R8X9C2tYTIJ2CLsChAKIAnYEpaP2HGj6D2m2/B7wDU9wUpYi5/AajvCyqYudxBncFU0FwTCMCcwVTQXJOAOoNpoLncQZ3B3IDmmgTUGcwRNNckoM5grkFzTQbqDKaC5iKEEEIIIYQQQgghi5D8AFw9eIA5hOpGAAAAAElFTkSuQmCC" />
                        </defs>
                    </svg>
                </span>
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center {{ $page == 'users' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <svg width="52" height="52" viewBox="0 0 52 52" fill="none"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="52" height="52" fill="url(#pattern0_563_442)" />
                        <defs>
                            <pattern id="pattern0_563_442" patternContentUnits="objectBoundingBox" width="1"
                                height="1">
                                <use xlink:href="#image0_563_442" transform="scale(0.0104167)" />
                            </pattern>
                            <image id="image0_563_442" width="96" height="96"
                                xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAAAsTAAALEwEAmpwYAAAEeUlEQVR4nO2dS4gcZRDHP42PqBfRHIMi3nziCxQS9aIEDOilq1oTFA8GRTDiReLBQchO1WYT1qghRCI+8CBevYm5qAdPPg4KoqIRgyDRTKZqdkk0afkmGxlnN9ndmZ6ub6brB99tl63+/7vqe2x3dQiO4ziO4ziO4ziO4ziLKA5su1in4XZleDKOuWZ2T/FhtmbxTzqlEQXWJj4ojG8pw1/KWPQOIfxdGJ4vGvddVN5frTlFo3Fhm7MNSrA3Ctwv+lJDCD4q9mSXWcc+1gjnNwoDC8GRlYi+yASGt62vYezoTOd3CsGMMB4eRPT+0Sa41/qakkd25bcIw5QS/FiG6H2l6D3r60uS+ZnsOiV8UQm+K1v0vjL0k/W1JkNrF16vhC8pwTejFL0vA06GOtPhLeuFcLsyfi6Ep6sSvneEutHe/ei6DuE2S9FrZ0BrT3aVEjwe19/C+Le16LUw4Njsw1f+JzrBSWuha2OAULZ14U4/YS1uLQ3QBEQdawOKma1XzM08dm2Hsju0mW+SadginD8nDK8o4T5l+EAZDp3r960FTdqAeAAlnD8gjC/0ihnX3sL4mzDMDxu4JiBqcgZ019uM+5WxNerANQFRkzJAKH9WGaWqwDUBUZMwIJ6TK8OrVQeuCYiahAHK0LAIXBMQ1dwAIbxfGE65AWhkAMMXVneO1j0D4t1vGbjW3YBRTrzqBiyPMHzpBqBlBuAfbgAazgEjXv2ozwHLZoDp5KUJTKyrGfH5olIzwA3AVRoQKwa8UTSeWOsGsGE2EHxS7N10qWcAW5qA+9wAtpwP4JRwdrPPAWxail53A9jQAMZv3QA2LEOEc24AWxoA6gawl6Bl75QwITvhJcZrngFsuAydgpvcAB7TJaifBeEw4n9cNLJLhjbAj6Nx1WUn1v1SzoEWMsD/IcMrN6A9ld0QykQIv6qiXoZz3wA29bvk6xgYJZx1A9DOAH8sBW0zIBJfgPMShHYGtKdxoxD+43MA2hgQUcKX3QC0M6AowgWxsYWvgtDGgLN0mtnTStj2ZSjaGBCZ4+yaeM6hDMd9H4DVG3CWorH58jPvDnTfF/ONGBu9pqoEt7oBWH0G/K93g2dAYWZARBg7ozhDEW9VsDKU4ftRHmId82YdyxpwqKpTxJa3q1mMEL5TlQG9HG8+crU3bOoaADstDOil1i3LujvkhAJvxaZ9DDuE8etaNO1Tzh9KyQCLtpWxF2mwYtjNWKi6cSvjD6VnAOO7wYo4GY6DAUu2Lib4pQwDYtPvYMkwm7Ew7s27CQ5aX8NQm7GQCGPdvn6YzVhI9AMO3ZZsBAeV8M8lhD/SZnwmmQ84DLMZC4kTRVbKb+s2HSTM4udMYraElBh0MyaEp61jnwiUs6cGzICj1rFPBJ0m3DWQAQSfWsc+EcSaKAw/D1CCtlvHPjGc+c7WKsRnPJzEEm5SiM8PKeH7KxMf5jsMd1vHPJlfnSOcPd+RsDD86uJXcEAnhG/Gg6+FPtJHlfGzWPPjIy3BcRzHcRzHcRzHcZxQL/4FTO+Is5sl4I4AAAAASUVORK5CYII=" />
                        </defs>
                    </svg>
                </span>
                Users
            </a>
            <a href="{{ route('admin.wallets.index') }}"
                class="flex items-center {{ $page == 'wallet' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <svg width="52" height="52" viewBox="0 0 52 52" fill="none"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="52" height="52" fill="url(#pattern0_563_457)" />
                        <defs>
                            <pattern id="pattern0_563_457" patternContentUnits="objectBoundingBox" width="1"
                                height="1">
                                <use xlink:href="#image0_563_457" transform="scale(0.0104167)" />
                            </pattern>
                            <image id="image0_563_457" width="96" height="96"
                                xlink:href="data:image/png;base64,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" />
                        </defs>
                    </svg>

                </span>
                Wallet
            </a>
            <a href="{{ route('admin.adaccounts.index') }}"
                class="flex items-center {{ $page == 'adaccounts' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <svg width="52" height="52" viewBox="0 0 52 52" fill="none"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="52" height="52" fill="url(#pattern0_563_442)" />
                        <defs>
                            <pattern id="pattern0_563_442" patternContentUnits="objectBoundingBox" width="1"
                                height="1">
                                <use xlink:href="#image0_563_442" transform="scale(0.0104167)" />
                            </pattern>
                            <image id="image0_563_442" width="96" height="96"
                                xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAAAsTAAALEwEAmpwYAAAEeUlEQVR4nO2dS4gcZRDHP42PqBfRHIMi3nziCxQS9aIEDOilq1oTFA8GRTDiReLBQchO1WYT1qghRCI+8CBevYm5qAdPPg4KoqIRgyDRTKZqdkk0afkmGxlnN9ndmZ6ub6brB99tl63+/7vqe2x3dQiO4ziO4ziO4ziO4ziLKA5su1in4XZleDKOuWZ2T/FhtmbxTzqlEQXWJj4ojG8pw1/KWPQOIfxdGJ4vGvddVN5frTlFo3Fhm7MNSrA3Ctwv+lJDCD4q9mSXWcc+1gjnNwoDC8GRlYi+yASGt62vYezoTOd3CsGMMB4eRPT+0Sa41/qakkd25bcIw5QS/FiG6H2l6D3r60uS+ZnsOiV8UQm+K1v0vjL0k/W1JkNrF16vhC8pwTejFL0vA06GOtPhLeuFcLsyfi6Ep6sSvneEutHe/ei6DuE2S9FrZ0BrT3aVEjwe19/C+Le16LUw4Njsw1f+JzrBSWuha2OAULZ14U4/YS1uLQ3QBEQdawOKma1XzM08dm2Hsju0mW+SadginD8nDK8o4T5l+EAZDp3r960FTdqAeAAlnD8gjC/0ihnX3sL4mzDMDxu4JiBqcgZ019uM+5WxNerANQFRkzJAKH9WGaWqwDUBUZMwIJ6TK8OrVQeuCYiahAHK0LAIXBMQ1dwAIbxfGE65AWhkAMMXVneO1j0D4t1vGbjW3YBRTrzqBiyPMHzpBqBlBuAfbgAazgEjXv2ozwHLZoDp5KUJTKyrGfH5olIzwA3AVRoQKwa8UTSeWOsGsGE2EHxS7N10qWcAW5qA+9wAtpwP4JRwdrPPAWxail53A9jQAMZv3QA2LEOEc24AWxoA6gawl6Bl75QwITvhJcZrngFsuAydgpvcAB7TJaifBeEw4n9cNLJLhjbAj6Nx1WUn1v1SzoEWMsD/IcMrN6A9ld0QykQIv6qiXoZz3wA29bvk6xgYJZx1A9DOAH8sBW0zIBJfgPMShHYGtKdxoxD+43MA2hgQUcKX3QC0M6AowgWxsYWvgtDGgLN0mtnTStj2ZSjaGBCZ4+yaeM6hDMd9H4DVG3CWorH58jPvDnTfF/ONGBu9pqoEt7oBWH0G/K93g2dAYWZARBg7ozhDEW9VsDKU4ftRHmId82YdyxpwqKpTxJa3q1mMEL5TlQG9HG8+crU3bOoaADstDOil1i3LujvkhAJvxaZ9DDuE8etaNO1Tzh9KyQCLtpWxF2mwYtjNWKi6cSvjD6VnAOO7wYo4GY6DAUu2Lib4pQwDYtPvYMkwm7Ew7s27CQ5aX8NQm7GQCGPdvn6YzVhI9AMO3ZZsBAeV8M8lhD/SZnwmmQ84DLMZC4kTRVbKb+s2HSTM4udMYraElBh0MyaEp61jnwiUs6cGzICj1rFPBJ0m3DWQAQSfWsc+EcSaKAw/D1CCtlvHPjGc+c7WKsRnPJzEEm5SiM8PKeH7KxMf5jsMd1vHPJlfnSOcPd+RsDD86uJXcEAnhG/Gg6+FPtJHlfGzWPPjIy3BcRzHcRzHcRzHcZxQL/4FTO+Is5sl4I4AAAAASUVORK5CYII=" />
                        </defs>
                    </svg>
                </span>
                Ad Accounts
            </a>
            <a href="{{ route('admin.rockads.accounts.index') }}"
                class="flex items-center {{ $page == 'rockads' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <svg width="52" height="52" viewBox="0 0 52 52" fill="none"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="52" height="52" fill="url(#pattern0_563_442)" />
                        <defs>
                            <pattern id="pattern0_563_442" patternContentUnits="objectBoundingBox" width="1"
                                height="1">
                                <use xlink:href="#image0_563_442" transform="scale(0.0104167)" />
                            </pattern>
                            <image id="image0_563_442" width="96" height="96"
                                xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAAAsTAAALEwEAmpwYAAAEeUlEQVR4nO2dS4gcZRDHP42PqBfRHIMi3nziCxQS9aIEDOilq1oTFA8GRTDiReLBQchO1WYT1qghRCI+8CBevYm5qAdPPg4KoqIRgyDRTKZqdkk0afkmGxlnN9ndmZ6ub6brB99tl63+/7vqe2x3dQiO4ziO4ziO4ziO4ziLKA5su1in4XZleDKOuWZ2T/FhtmbxTzqlEQXWJj4ojG8pw1/KWPQOIfxdGJ4vGvddVN5frTlFo3Fhm7MNSrA3Ctwv+lJDCD4q9mSXWcc+1gjnNwoDC8GRlYi+yASGt62vYezoTOd3CsGMMB4eRPT+0Sa41/qakkd25bcIw5QS/FiG6H2l6D3r60uS+ZnsOiV8UQm+K1v0vjL0k/W1JkNrF16vhC8pwTejFL0vA06GOtPhLeuFcLsyfi6Ep6sSvneEutHe/ei6DuE2S9FrZ0BrT3aVEjwe19/C+Le16LUw4Njsw1f+JzrBSWuha2OAULZ14U4/YS1uLQ3QBEQdawOKma1XzM08dm2Hsju0mW+SadginD8nDK8o4T5l+EAZDp3r960FTdqAeAAlnD8gjC/0ihnX3sL4mzDMDxu4JiBqcgZ019uM+5WxNerANQFRkzJAKH9WGaWqwDUBUZMwIJ6TK8OrVQeuCYiahAHK0LAIXBMQ1dwAIbxfGE65AWhkAMMXVneO1j0D4t1vGbjW3YBRTrzqBiyPMHzpBqBlBuAfbgAazgEjXv2ozwHLZoDp5KUJTKyrGfH5olIzwA3AVRoQKwa8UTSeWOsGsGE2EHxS7N10qWcAW5qA+9wAtpwP4JRwdrPPAWxail53A9jQAMZv3QA2LEOEc24AWxoA6gawl6Bl75QwITvhJcZrngFsuAydgpvcAB7TJaifBeEw4n9cNLJLhjbAj6Nx1WUn1v1SzoEWMsD/IcMrN6A9ld0QykQIv6qiXoZz3wA29bvk6xgYJZx1A9DOAH8sBW0zIBJfgPMShHYGtKdxoxD+43MA2hgQUcKX3QC0M6AowgWxsYWvgtDGgLN0mtnTStj2ZSjaGBCZ4+yaeM6hDMd9H4DVG3CWorH58jPvDnTfF/ONGBu9pqoEt7oBWH0G/K93g2dAYWZARBg7ozhDEW9VsDKU4ftRHmId82YdyxpwqKpTxJa3q1mMEL5TlQG9HG8+crU3bOoaADstDOil1i3LujvkhAJvxaZ9DDuE8etaNO1Tzh9KyQCLtpWxF2mwYtjNWKi6cSvjD6VnAOO7wYo4GY6DAUu2Lib4pQwDYtPvYMkwm7Ew7s27CQ5aX8NQm7GQCGPdvn6YzVhI9AMO3ZZsBAeV8M8lhD/SZnwmmQ84DLMZC4kTRVbKb+s2HSTM4udMYraElBh0MyaEp61jnwiUs6cGzICj1rFPBJ0m3DWQAQSfWsc+EcSaKAw/D1CCtlvHPjGc+c7WKsRnPJzEEm5SiM8PKeH7KxMf5jsMd1vHPJlfnSOcPd+RsDD86uJXcEAnhG/Gg6+FPtJHlfGzWPPjIy3BcRzHcRzHcRzHcZxQL/4FTO+Is5sl4I4AAAAASUVORK5CYII=" />
                        </defs>
                    </svg>
                </span>
                RockAds Accounts
            </a>
            <a href="{{ route('admin.meta.accounts.index') }}"
                class="flex items-center {{ $page == 'meta-accounts' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <svg width="52" height="52" viewBox="0 0 52 52" fill="none"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <rect width="52" height="52" fill="url(#pattern0_563_442)" />
                        <defs>
                            <pattern id="pattern0_563_442" patternContentUnits="objectBoundingBox" width="1"
                                height="1">
                                <use xlink:href="#image0_563_442" transform="scale(0.0104167)" />
                            </pattern>
                            <image id="image0_563_442" width="96" height="96"
                                xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAAAsTAAALEwEAmpwYAAAEeUlEQVR4nO2dS4gcZRDHP42PqBfRHIMi3nziCxQS9aIEDOilq1oTFA8GRTDiReLBQchO1WYT1qghRCI+8CBevYm5qAdPPg4KoqIRgyDRTKZqdkk0afkmGxlnN9ndmZ6ub6brB99tl63+/7vqe2x3dQiO4ziO4ziO4ziO4ziLKA5su1in4XZleDKOuWZ2T/FhtmbxTzqlEQXWJj4ojG8pw1/KWPQOIfxdGJ4vGvddVN5frTlFo3Fhm7MNSrA3Ctwv+lJDCD4q9mSXWcc+1gjnNwoDC8GRlYi+yASGt62vYezoTOd3CsGMMB4eRPT+0Sa41/qakkd25bcIw5QS/FiG6H2l6D3r60uS+ZnsOiV8UQm+K1v0vjL0k/W1JkNrF16vhC8pwTejFL0vA06GOtPhLeuFcLsyfi6Ep6sSvneEutHe/ei6DuE2S9FrZ0BrT3aVEjwe19/C+Le16LUw4Njsw1f+JzrBSWuha2OAULZ14U4/YS1uLQ3QBEQdawOKma1XzM08dm2Hsju0mW+SadginD8nDK8o4T5l+EAZDp3r960FTdqAeAAlnD8gjC/0ihnX3sL4mzDMDxu4JiBqcgZ019uM+5WxNerANQFRkzJAKH9WGaWqwDUBUZMwIJ6TK8OrVQeuCYiahAHK0LAIXBMQ1dwAIbxfGE65AWhkAMMXVneO1j0D4t1vGbjW3YBRTrzqBiyPMHzpBqBlBuAfbgAazgEjXv2ozwHLZoDp5KUJTKyrGfH5olIzwA3AVRoQKwa8UTSeWOsGsGE2EHxS7N10qWcAW5qA+9wAtpwP4JRwdrPPAWxail53A9jQAMZv3QA2LEOEc24AWxoA6gawl6Bl75QwITvhJcZrngFsuAydgpvcAB7TJaifBeEw4n9cNLJLhjbAj6Nx1WUn1v1SzoEWMsD/IcMrN6A9ld0QykQIv6qiXoZz3wA29bvk6xgYJZx1A9DOAH8sBW0zIBJfgPMShHYGtKdxoxD+43MA2hgQUcKX3QC0M6AowgWxsYWvgtDGgLN0mtnTStj2ZSjaGBCZ4+yaeM6hDMd9H4DVG3CWorH58jPvDnTfF/ONGBu9pqoEt7oBWH0G/K93g2dAYWZARBg7ozhDEW9VsDKU4ftRHmId82YdyxpwqKpTxJa3q1mMEL5TlQG9HG8+crU3bOoaADstDOil1i3LujvkhAJvxaZ9DDuE8etaNO1Tzh9KyQCLtpWxF2mwYtjNWKi6cSvjD6VnAOO7wYo4GY6DAUu2Lib4pQwDYtPvYMkwm7Ew7s27CQ5aX8NQm7GQCGPdvn6YzVhI9AMO3ZZsBAeV8M8lhD/SZnwmmQ84DLMZC4kTRVbKb+s2HSTM4udMYraElBh0MyaEp61jnwiUs6cGzICj1rFPBJ0m3DWQAQSfWsc+EcSaKAw/D1CCtlvHPjGc+c7WKsRnPJzEEm5SiM8PKeH7KxMf5jsMd1vHPJlfnSOcPd+RsDD86uJXcEAnhG/Gg6+FPtJHlfGzWPPjIy3BcRzHcRzHcRzHcZxQL/4FTO+Is5sl4I4AAAAASUVORK5CYII=" />
                        </defs>
                    </svg>
                </span>
                Meta Accounts
            </a>
            <a href="{{ route('admin.business-managers.index') }}"
                class="flex items-center {{ $page == 'business-managers' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <svg width="52" height="52" viewBox="0 0 52 52" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M26 23.8333C31.0627 23.8333 35.1667 19.7293 35.1667 14.6667C35.1667 9.604 31.0627 5.5 26 5.5C20.9373 5.5 16.8333 9.604 16.8333 14.6667C16.8333 19.7293 20.9373 23.8333 26 23.8333Z"
                            stroke="currentColor" stroke-width="2" />
                        <path
                            d="M41.1667 46.5H10.8333C8.99238 46.5 7.5 45.0076 7.5 43.1667V41.8333C7.5 36.7707 11.604 32.6667 16.6667 32.6667H35.3333C40.396 32.6667 44.5 36.7707 44.5 41.8333V43.1667C44.5 45.0076 43.0076 46.5 41.1667 46.5Z"
                            stroke="currentColor" stroke-width="2" />
                    </svg>
                </span>
                Business Managers
            </a>
            <a href="{{ route('admin.adminsettings.index') }}"
                class="flex items-center {{ $page == 'settings' ? 'active-nav-link' : '' }} opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                <span class="mr-3">
                    <svg width="52" height="52" viewBox="0 0 52 52" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20.8 26C20.8 23.1281 23.1281 20.8 26 20.8C28.8719 20.8 31.2 23.1281 31.2 26C31.2 28.8719 28.8719 31.2 26 31.2C23.1281 31.2 20.8 28.8719 20.8 26Z"
                            stroke="currentColor" stroke-width="2" />
                        <path
                            d="M41.6 26C41.6 34.7279 34.7279 41.6 26 41.6C17.2721 41.6 10.4 34.7279 10.4 26C10.4 17.2721 17.2721 10.4 26 10.4C34.7279 10.4 41.6 17.2721 41.6 26Z"
                            stroke="currentColor" stroke-width="2" />
                    </svg>
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
        <footer class="flex bg-gradient-to-r from-gray-200 to-blue-800 bg-opacity-30 justify-between items-center w-full max-w-screen bg-white dark:bg-gray-800 text-right p-4">
            <p>Billing is developed by <a href="https://bloomdigitmedia.com" class="underline text-black dark:text-white">BLOOM
                    DIGITAL MEDIA LTD.</a> 2024. All Rights Reserved</p>
            <div class="flex">
                <a href="https://www.instagram.com/bloom_digitalmedia?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                    target="_blank"><img src="/images/Instagram.png" alt="Instagram Link"/></a>
                <a href="https://x.com/bloomdigitmedia?s=20" target="_blank"><img src="/images/TwitterX.png"
                        alt="X Link" /></a>
                <a href="https://www.facebook.com/bloomdigitmedia/" target="_blank"><img
                        src="/images/Facebook.png" alt="Facebook Link" /></a>
                <a href="https://www.linkedin.com/company/bloom-digital-media-nigeria/" target="_blank"><img
                        src="/images/LinkedIn.png" alt="LinkedIn Link" /></a>
            </div>
        </footer>
    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
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