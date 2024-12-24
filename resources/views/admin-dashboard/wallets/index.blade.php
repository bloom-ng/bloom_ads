<x-admin-layout page="wallet">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">All Wallets</h1>

            <div class="w-full">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                User</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Organization</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Currency</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Balance</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Created At</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wallets as $wallet)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ $wallet->organization->users->first()->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ $wallet->organization->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ $wallet->currency }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ number_format($wallet->getBalance(), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ $wallet->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    <button onclick="openCreditModal({{ $wallet->id }})"
                                        class="text-green-600 hover:text-green-900 mr-2">
                                        Credit
                                    </button>
                                    <button onclick="openDebitModal({{ $wallet->id }})"
                                        class="text-red-600 hover:text-red-900">
                                        Debit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $wallets->links() }}
            </div>
        </main>
    </div>

    <!-- Credit Modal -->
    <div id="creditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Credit Wallet</h2>
            <form id="creditForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" name="amount" required step="0.01" min="0.01"
                        class="w-full p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <input type="text" name="description" required class="w-full p-2 border rounded">
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">
                    Credit Wallet
                </button>
            </form>
            <button onclick="closeModal('creditModal')"
                class="w-full mt-4 border border-gray-300 text-gray-700 py-2 px-4 rounded">
                Cancel
            </button>
        </div>
    </div>

    <!-- Debit Modal -->
    <div id="debitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Debit Wallet</h2>
            <form id="debitForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" name="amount" required step="0.01" min="0.01"
                        class="w-full p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <input type="text" name="description" required class="w-full p-2 border rounded">
                </div>
                <button type="submit" class="w-full bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
                    Debit Wallet
                </button>
            </form>
            <button onclick="closeModal('debitModal')"
                class="w-full mt-4 border border-gray-300 text-gray-700 py-2 px-4 rounded">
                Cancel
            </button>
        </div>
    </div>

    <script>
        function openCreditModal(walletId) {
            document.getElementById('creditForm').action = `/admin/wallets/${walletId}/credit`;
            document.getElementById('creditModal').style.display = 'flex';
        }

        function openDebitModal(walletId) {
            document.getElementById('debitForm').action = `/admin/wallets/${walletId}/debit`;
            document.getElementById('debitModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</x-admin-layout>
