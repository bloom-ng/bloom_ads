<x-user-layout page="wallet">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Organization Wallets</h1>

            <div class="w-full mt-6">
                <div class="bg-white overflow-auto p-6 rounded-lg shadow-sm">
                    @if (!$organization)
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">Please select a current organization to manage wallets.</p>
                            <a href="{{ route('settings.index') }}"
                                class="bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Go to Settings
                            </a>
                        </div>
                    @else
                        <!-- Create Wallet Form -->
                        @if (in_array($userRole, ['owner', 'admin', 'finance']))
                            <form method="POST" action="{{ route('wallet.create') }}" class="mb-8">
                                @csrf
                                <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                        Create New Wallet
                                    </label>
                                    <div class="flex gap-4">
                                        <select name="currency" id="currency" required
                                            class="rounded-md border-gray-300 shadow-sm focus:border-[#F48857] focus:ring-[#F48857] sm:text-sm">
                                            <option value="NGN">NGN</option>
                                            <option value="USD">USD</option>
                                            <option value="GBP">GBP</option>
                                        </select>
                                        <button type="submit"
                                            class="bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            Create Wallet
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        <!-- Existing Wallets -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse ($organization->wallets as $wallet)
                                <div class="bg-white border rounded-lg shadow-sm p-6">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg font-semibold">{{ $wallet->currency }} Wallet</h3>
                                        <span class="text-2xl font-bold">{{ number_format($wallet->balance, 2) }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500">No wallets created yet.</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-user-layout>
