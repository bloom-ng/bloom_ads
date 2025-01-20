<x-admin-layout page="organizations">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6 bg-body">
            <div class="flex items-center justify-between pb-6">
                <div>
                    <h1 class="text-3xl text-black text pb-6">Wallets of {{ $organization->name }}</h1>
                </div>
                <a href="{{ route('admin.organizations.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Organizations
                </a>
            </div>
            
            <div class="w-full mt-6">
                <div class="bg-white shadow-md rounded my-6">
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">ID</th>
                                <th class="py-3 px-6 text-left">Balance</th>
                                <th class="py-3 px-6 text-left">Currency</th>
                                <th class="py-3 px-6 text-center">Created At</th>
                                <th class="py-3 px-6 text-center">Updated At</th>
                                <th class="py-3 px-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($wallets as $wallet)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $wallet->id }}</td>
                                <td class="py-3 px-6 text-left">{{ number_format($wallet->getBalance(), 2) }}</td>
                                <td class="py-3 px-6 text-left">{{ $wallet->currency }}</td>
                                <td class="py-3 px-6 text-center">{{ $wallet->created_at->format('Y-m-d') }}</td>
                                <td class="py-3 px-6 text-center">{{ $wallet->updated_at->format('Y-m-d') }}</td>
                                <td class="py-3 px-6 text-center">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button @click="open = !open" class="flex items-center text-gray-600 hover:text-gray-700">
                                            <span class="mr-2">Actions</span>
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                            </svg>
                                        </button>
                                        <div x-show="open" 
                                             @click.away="open = false"
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 overflow-hidden"
                                             style="display: none;">
                                            <div class="py-1">
                                                <button onclick="openFundModal('{{ $wallet->id }}', '{{ $wallet->currency }}')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Fund Wallet
                                                </button>
                                                <a href="{{ route('admin.wallets.transactions', $wallet) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    View Transactions
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Fund Wallet Modal -->
    <div id="fundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-medium">Fund Wallet</h3>
                <button onclick="closeFundModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="" method="POST" id="fundForm" class="space-y-4">
                @csrf
                <input type="hidden" id="walletId" name="wallet_id">
                
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                    <div class="relative">
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F48857] focus:ring-[#F48857] sm:text-sm"
                            placeholder="Enter amount">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm" id="currencyLabel"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" id="description" name="description" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F48857] focus:ring-[#F48857] sm:text-sm"
                        placeholder="Enter transaction description">
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeFundModal()"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-[#F48857] text-black px-4 py-2 rounded-md hover:bg-[#F48857]/80">
                        Fund Wallet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openFundModal(walletId, currency) {
            document.getElementById('fundModal').classList.remove('hidden');
            document.getElementById('fundModal').classList.add('flex');
            document.getElementById('walletId').value = walletId;
            document.getElementById('currencyLabel').textContent = currency;
            document.getElementById('fundForm').action = `/admin/wallets/${walletId}/credit`;
        }

        function closeFundModal() {
            document.getElementById('fundModal').classList.add('hidden');
            document.getElementById('fundModal').classList.remove('flex');
            document.getElementById('amount').value = '';
            document.getElementById('description').value = '';
        }
    </script>
</x-admin-layout>
