<x-admin-layout page="organizations">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl text-black pb-6">Wallet</h1>
                <div class="space-x-4">
                    <button onclick="openModal('creditModal')" 
                            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                        Fund Wallet
                    </button>
                    <button onclick="openModal('debitModal')" 
                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                        Debit Wallet
                    </button>
                </div>
            </div>

            <!-- Credit (Fund) Modal -->
            <div id="creditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg max-w-md w-full">
                    <h2 class="text-2xl font-bold mb-4">Fund Wallet</h2>
                    <form id="creditForm" method="POST">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                            <input type="number" name="amount" required step="0.01" min="0.01"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <input type="text" name="description" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="closeModal('creditModal')"
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                                Fund Wallet
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Debit Modal -->
            <div id="debitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg max-w-md w-full">
                    <h2 class="text-2xl font-bold mb-4">Debit Wallet</h2>
                    <form id="debitForm" method="POST">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                            <input type="number" name="amount" required step="0.01" min="0.01"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <input type="text" name="description" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="closeModal('debitModal')"
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                                Debit Wallet
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="w-full">
                <!-- Transactions Table -->
                <div class="bg-white shadow-md rounded my-6">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold mb-4">Transactions</h2>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->currency }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function openModal(modalId) {
            if (modalId === 'creditModal') {
                document.getElementById('creditForm').action = `/admin/wallets/{{ $wallet->id }}/credit`;
            } else if (modalId === 'debitModal') {
                document.getElementById('debitForm').action = `/admin/wallets/{{ $wallet->id }}/debit`;
            }
            document.getElementById(modalId).classList.remove('hidden');
            document.getElementById(modalId).classList.add('flex');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('flex');
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>
</x-admin-layout>