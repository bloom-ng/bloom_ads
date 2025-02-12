<x-user-layout page="dashboard">
    <div class="dashboard w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Dashboard</h1>
            <div class="mb-6">
                @php
                    $hour = date('G');
                    $greeting = '';
                    if ($hour >= 5 && $hour < 12) {
                        $greeting = 'Good morning';
                    } elseif ($hour >= 12 && $hour < 18) {
                        $greeting = 'Good afternoon';
                    } else {
                        $greeting = 'Good evening';
                    }
                @endphp
                <h2 class="text-2xl font-semibold">{{ $greeting }} {{ auth()->user()->name }}! 👋</h2>
                <p class="text-lg font-medium">Welcome to Billing</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Organizations -->
                <x-card 
                    iconSvg='<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>'
                    currency="Total Organizations"
                    :balance="$totalOrganizations"
                />

                <!-- Total Money Spent -->
                <x-card 
                    iconSvg='<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>'
                    currency="Total Money Spent"
                    :balance="$totalSpent"
                />

                <!-- Total Money Credited -->
                <x-card 
                    iconSvg='<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>'
                    currency="Total Money Credited"
                    :balance="$totalCredited"
                />
            </div>

            <!-- Action Cards -->
            <div class="mt-8">
                <h2 class="text-2xl font-semibold mb-6">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Make a deposit -->
                    <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex flex-col space-y-4">
                            <div class="bg-pink-50 w-12 h-12 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Make a deposit</h3>
                                <p class="text-gray-500 text-sm">Deposit money quickly on your main balance and start allocating ad budgets</p>
                            </div>
                            <a href="{{ route('wallet.index') }}" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Request an ad account -->
                    <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex flex-col space-y-4">
                            <div class="bg-purple-50 w-12 h-12 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Request an ad account</h3>
                                <p class="text-gray-500 text-sm">Request an advertising account in a few clicks, get it and start promoting your business</p>
                            </div>
                            <a href="{{ route('adaccounts.create') }}" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Request a top-up -->
                    <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex flex-col space-y-4">
                            <div class="bg-teal-50 w-12 h-12 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Request a top-up</h3>
                                <p class="text-gray-500 text-sm">Top up your advertising accounts in just a few minutes</p>
                            </div>
                            <a href="{{ route('adaccounts.index') }}" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- You can add more dashboard content below -->
        </main>
    </div>
</x-user-layout>
