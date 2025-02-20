<x-user-layout page="adaccounts">
    <div class="mt-2 space-y-6 overflow-y-scroll">
        <!-- Action Buttons -->
        <div class="flex space-x-4 pl-3">
            <button onclick="openDepositModal()" class="button px-6 py-3 rounded-md ">
                Deposit Funds
            </button>
            <button onclick="openWithdrawModal()" class="btn px-6 py-3 rounded-md">
                Withdraw Funds
            </button>
        </div>

        <!-- Deposit Modal -->
        <div id="depositModal"
            class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 overflow-auto py-6">
            <div class="dashboard text p-8 rounded-lg w-full max-w-md">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-medium">Deposit Funds</h3>
                    <button onclick="closeDepositModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('adaccounts.deposit', $adAccount->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium">Select Wallet</label>
                            <select name="wallet_id" id="depositWalletId" required
                                class="text-gray-900 mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                onchange="updateDepositPreview()">
                                @foreach ($wallets->where('currency', $adAccount->currency) as $wallet)
                                    <option value="{{ $wallet->id }}">
                                        {{ $wallet->currency }} Wallet (Balance:
                                        {{ number_format($wallet->getBalance(), 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Amount</label>
                            <input type="number" name="amount" id="depositAmount" min="1" step="0.01"
                                required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                oninput="updateDepositPreview()">
                        </div>

                        <!-- Fee Preview -->
                        <div class="p-4 rounded-md">
                            <h4 class="text-sm font-medium mb-2">Transaction Preview</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Amount:</span>
                                    <span id="previewDepositAmount">0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>VAT ({{ $adAccount->getVatRate() }}%):</span>
                                    <span id="previewDepositVat">0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Service Fee ({{ $adAccount->getServiceFeeRate() }}%):</span>
                                    <span id="previewDepositServiceFee">0.00</span>
                                </div>
                                <div class="flex justify-between font-medium border-t pt-2 mt-2">
                                    <span>Total Amount:</span>
                                    <span id="previewDepositTotal">0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Spend Cap Progress Bar -->
                        <div class="p-4 rounded-md mt-4">
                            <h4 class="text-sm font-medium mb-2">Spend Cap Progress</h4>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                <div id="depositSpendCapProgress" class="bg-[#000080] h-2.5 rounded-full"
                                    style="width: 0%"></div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-400">
                                <span id="depositCurrentBalance">Current: 0.00</span>
                                <span id="depositSpendCap">Spend Cap: 0.00</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full btn px-4 py-2 rounded-md">
                            Confirm Deposit
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Withdraw Modal -->
        <div id="withdrawModal"
            class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 overflow-auto py-6">
            <div class="dashboard text p-8 rounded-lg w-full max-w-md">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-medium">Withdraw Funds</h3>
                    <button onclick="closeWithdrawModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('adaccounts.withdraw', $adAccount->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium">Select Wallet</label>
                            <select name="wallet_id" id="withdrawWalletId" required
                                class="text-gray-900 mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                onchange="updateWithdrawPreview()">
                                @foreach ($wallets->where('currency', $adAccount->currency) as $wallet)
                                    <option value="{{ $wallet->id }}">
                                        {{ $wallet->currency }} Wallet (Balance:
                                        {{ number_format($wallet->getBalance(), 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Amount</label>
                            <input type="number" name="amount" id="withdrawAmount" min="1" step="0.01"
                                required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                oninput="updateWithdrawPreview()">
                        </div>

                        <!-- Fee Preview -->
                        <div class="p-4 rounded-md">
                            <h4 class="text-sm font-medium mb-2">Transaction Preview</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Amount:</span>
                                    <span id="previewWithdrawAmount">0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>VAT ({{ $adAccount->getVatRate() }}%):</span>
                                    <span id="previewWithdrawVat">0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Service Fee ({{ $adAccount->getServiceFeeRate() }}%):</span>
                                    <span id="previewWithdrawServiceFee">0.00</span>
                                </div>
                                <div class="flex justify-between font-medium border-t pt-2 mt-2">
                                    <span>Total Amount:</span>
                                    <span id="previewWithdrawTotal">0.00</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-md mt-4">
                            <h4 class="text-sm font-medium mb-2">Spend Cap Progress</h4>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                                <div id="withdrawSpendCapProgress" class="bg-[#000080] h-2.5 rounded-full"
                                    style="width: 0%"></div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-400">
                                <span id="withdrawCurrentBalance">Current: 0.00</span>
                                <span id="withdrawSpendCap">Spend Cap: 0.00</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full btn text-white px-4 py-2 rounded-md">
                            Confirm Withdrawal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Balance -->
        <div class="dashboard text p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Account Balance</h3>
            <p class="text-2xl font-bold">{{ number_format($adAccount->getBalance(), 2) }} {{ $adAccount->currency }}
            </p>
        </div>

        <!-- Meta Account -->
        @if ($providerInfo['_provider'] == 'meta')
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">Meta Account</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Name</p>
                        <p class="font-medium">{{ $providerInfo['_meta_ad_account']['name'] }}</p>
                    </div>

                    <div>
                        <p class="text-gray-600">Status</p>
                        <p class="font-medium">
                            <span class="px-2 py-1 rounded-full text-sm">
                                {{ \App\Services\Meta\Data\AdAccountMapping::accountStatusMapping()[$providerInfo['_meta_ad_account']['account_status']] }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Amount Spent</p>
                        <p class="font-medium"> {{ $providerInfo['_meta_ad_account']['currency'] }}
                            {{ $providerInfo['_meta_ad_account']['amount_spent'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Remaininig Balance</p>
                        <p class="font-medium">
                            {{ $providerInfo['_meta_ad_account']['currency'] }}
                            {{ abs(round(($providerInfo['_meta_ad_account']['spend_cap'] - $providerInfo['_meta_ad_account']['amount_spent']) / 100, 2)) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
        <!-- Transactions History -->
        <div class="dashboard text p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Transaction History</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="table-header">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">VAT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service Fee
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="table-body divide-y divide-gray-200">
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

    <script>
        // Modal functions
        function openDepositModal() {
            document.getElementById('depositModal').style.display = 'flex';
            updateSpendCapInfo('deposit');
        }

        function closeDepositModal() {
            document.getElementById('depositModal').style.display = 'none';
        }

        function openWithdrawModal() {
            document.getElementById('withdrawModal').style.display = 'flex';
            updateSpendCapInfo('withdraw');
        }

        function closeWithdrawModal() {
            document.getElementById('withdrawModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('depositModal')) {
                closeDepositModal();
            }
            if (event.target == document.getElementById('withdrawModal')) {
                closeWithdrawModal();
            }
        }

        // Fee calculation functions
        function calculateFees(amount) {
            const vatRate = {{ $adAccount->getVatRate() }};
            const serviceFeeRate = {{ $adAccount->getServiceFeeRate() }};

            const vat = (amount * vatRate) / 100;
            const serviceFee = (amount * serviceFeeRate) / 100;
            const total = amount + vat + serviceFee;

            return {
                amount: amount,
                vat: vat,
                serviceFee: serviceFee,
                total: total
            };
        }

        function updateDepositPreview() {
            const amount = parseFloat(document.getElementById('depositAmount').value) || 0;
            const fees = calculateFees(amount);

            document.getElementById('previewDepositAmount').textContent = fees.amount.toFixed(2);
            document.getElementById('previewDepositVat').textContent = fees.vat.toFixed(2);
            document.getElementById('previewDepositServiceFee').textContent = fees.serviceFee.toFixed(2);
            document.getElementById('previewDepositTotal').textContent = fees.total.toFixed(2);
        }

        function updateWithdrawPreview() {
            const amount = parseFloat(document.getElementById('withdrawAmount').value) || 0;
            const fees = calculateFees(amount);

            document.getElementById('previewWithdrawAmount').textContent = fees.amount.toFixed(2);
            document.getElementById('previewWithdrawVat').textContent = fees.vat.toFixed(2);
            document.getElementById('previewWithdrawServiceFee').textContent = fees.serviceFee.toFixed(2);
            document.getElementById('previewWithdrawTotal').textContent = fees.total.toFixed(2);
        }

        async function updateSpendCapInfo(modalType) {
            try {

                const response = await fetch(`/api/ad-accounts/{{ $adAccount->id }}/spend-cap`, {
                    headers: {
                        'Authorization': `Bearer ${document.querySelector('meta[name="csrf-token"]').content}`
                    }
                });
                const data = await response.json();

                const spendCap = parseFloat(data.spend_cap) || 0;
                const balance = parseFloat(data.balance) || 0;
                const percentage = spendCap > 0 ? (balance / spendCap * 100) : 0;

                document.getElementById(`${modalType}SpendCapProgress`).style.width = `${Math.min(percentage, 100)}%`;
                document.getElementById(`${modalType}CurrentBalance`).textContent =
                    `Current: ${balance.toFixed(2)} {{ $adAccount->currency }}`;
                document.getElementById(`${modalType}SpendCap`).textContent =
                    `Spend Cap: ${spendCap.toFixed(2)/ 100} {{ $adAccount->currency }}`;
            } catch (error) {
                console.error('Error fetching spend cap:', error);
            }
        }
    </script>
</x-user-layout>
