<x-user-layout page="adaccounts">
    <div class="mt-8 space-y-6">
        <!-- Deposit Form -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Deposit Funds</h3>
            <form action="{{ route('adaccounts.deposit', $adAccount->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" name="amount" min="1" step="0.01" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Wallet</label>
                        <select name="wallet_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach ($wallets->where('currency', $adAccount->currency) as $wallet)
                                <option value="{{ $wallet->id }}">
                                    {{ $wallet->currency }} Wallet (Balance: {{ number_format($wallet->balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-[#F48857] text-black px-4 py-2 rounded-md hover:bg-[#F48857]/80">
                        Deposit
                    </button>
                </div>
            </form>
        </div>

        <!-- Withdraw Form -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Withdraw Funds</h3>
            <form action="{{ route('adaccounts.withdraw', $adAccount->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" name="amount" min="1" step="0.01" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Wallet</label>
                        <select name="wallet_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach ($wallets->where('currency', $adAccount->currency) as $wallet)
                                <option value="{{ $wallet->id }}">
                                    {{ $wallet->currency }} Wallet (Balance: {{ number_format($wallet->balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-[#F48857] text-black px-4 py-2 rounded-md hover:bg-[#F48857]/80">
                        Withdraw
                    </button>
                </div>
            </form>
        </div>

        <!-- Transactions History -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Transaction History</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">VAT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service Fee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($adAccount->transactions()->latest()->get() as $transaction)
                            <tr>
                                <td class="px-6 py-4">{{ ucfirst($transaction->type) }}</td>
                                <td class="px-6 py-4">{{ number_format($transaction->amount, 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($transaction->vat, 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($transaction->service_fee, 2) }}</td>
                                <td class="px-6 py-4">{{ number_format($transaction->total_amount, 2) }}</td>
                                <td class="px-6 py-4">{{ ucfirst($transaction->status) }}</td>
                                <td class="px-6 py-4">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-user-layout>
