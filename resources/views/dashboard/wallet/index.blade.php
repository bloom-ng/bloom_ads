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
                                        <span
                                            class="text-2xl font-bold">{{ number_format($wallet->calculated_balance, 2) }}</span>
                                    </div>

                                    <div class="mt-4 space-y-2">
                                        <!-- Fund Button - For all wallets -->
                                        <button
                                            onclick="openFundModal('{{ $wallet->id }}', '{{ $wallet->currency }}')"
                                            class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            Fund Wallet
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500">No wallets found.</p>
                            @endforelse
                        </div>

                        <!-- Add Global Convert Button -->
                        <div class="mt-8">
                            <button onclick="openTransferModal()"
                                class="bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Convert Currency
                            </button>
                        </div>

                        <!-- Transaction History Section -->
                        <div class="mt-8">
                            <h2 class="text-2xl font-semibold mb-4">Transaction History</h2>
                            <div class="bg-white border rounded-lg shadow-sm overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Currency</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($transactions as $transaction)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ ucfirst($transaction->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if ($transaction->status === 'completed')
                                                        <a href="{{ route('wallet.transaction.receipt', $transaction) }}"
                                                            class="text-gray-600 hover:text-gray-900">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7"
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    No transactions found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                @if ($transactions->hasPages())
                                    <div class="px-6 py-4 border-t border-gray-200">
                                        {{ $transactions->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Fund Wallet Modal -->
    <div id="fundWalletModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Fund Wallet</h2>
            <p class="mb-4">Select your preferred payment method:</p>

            <div class="space-y-4 mb-6">
                <form action="{{ route('wallet.fund.flutterwave') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="wallet_id" id="modalWalletId">
                    <input type="hidden" name="currency" value="NGN">
                    <input type="hidden" name="wallet_currency" id="modalWalletCurrency">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount (NGN)</label>
                        <input type="number" name="amount" id="fundAmount" placeholder="Enter amount in NGN" required
                            class="w-full p-2 border rounded" oninput="updateFundingPreview()">

                        <div id="fundingConversionPreview" class="text-sm text-gray-600 bg-gray-50 p-3 rounded mt-2"
                            style="display: none;">
                            <p id="fundingRateDisplay" class="mb-2"></p>
                            <p id="fundingConvertedAmountDisplay"></p>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded">
                        Pay with Flutterwave
                    </button>
                </form>

                <form action="{{ route('wallet.fund.paystack') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="wallet_id" id="modalWalletIdPaystack">
                    <input type="hidden" name="currency" value="NGN">
                    <input type="hidden" name="wallet_currency" id="modalWalletCurrencyPaystack">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount (NGN)</label>
                        <input type="number" name="amount" id="fundAmountPaystack"
                            placeholder="Enter amount in NGN" required class="w-full p-2 border rounded"
                            oninput="updateFundingPreview()">
                    </div>
                    <button type="submit"
                        class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded">
                        Pay with Paystack
                    </button>
                </form>
            </div>

            <button onclick="closeFundModal()"
                class="w-full mt-4 border border-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                Cancel
            </button>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div id="transferModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Convert Funds</h2>
            <form action="{{ route('wallet.transfer') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Currency</label>
                    <div class="relative">
                        <select name="source_currency" id="transferSourceCurrency" required
                            class="w-full pl-12 pr-20 py-2 border rounded appearance-none"
                            onchange="updateConversionPreview()">
                            @foreach ($organization->wallets as $wallet)
                                <option value="{{ $wallet->currency }}" data-wallet-id="{{ $wallet->id }}"
                                    data-balance="{{ $wallet->calculated_balance }}">
                                    {{ $wallet->currency }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <img id="sourceFlag" src="" alt="" class="w-6 h-6 rounded-full">
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                            Balance: <span id="sourceBalance" class="ml-1"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Currency</label>
                    <div class="relative">
                        <select name="destination_currency" id="transferDestinationCurrency" required
                            class="w-full pl-12 py-2 border rounded appearance-none"
                            onchange="updateConversionPreview()">
                            <option value="NGN">NGN</option>
                            <option value="USD">USD</option>
                            <option value="GBP">GBP</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <img id="destFlag" src="" alt="" class="w-6 h-6 rounded-full">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" name="amount" id="transferAmount" required
                        class="w-full p-2 border rounded" step="0.01" min="0"
                        oninput="updateConversionPreview()">
                </div>

                <div id="conversionPreview" class="text-sm text-gray-600 bg-gray-50 p-3 rounded">
                    <p id="rateDisplay" class="mb-2"></p>
                    <p id="convertedAmountDisplay"></p>
                </div>

                <button type="submit"
                    class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded">
                    Convert
                </button>
            </form>

            <button onclick="closeTransferModal()"
                class="w-full mt-4 border border-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                Cancel
            </button>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg max-w-2xl w-full">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Transaction Receipt</h2>
                <button onclick="closeReceiptModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="receiptContent" class="space-y-4">
                <!-- Receipt content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        const USD_RATE = {{ \App\Models\Wallet::getRate('usd') }};
        const GBP_RATE = {{ \App\Models\Wallet::getRate('gbp') }};

        function openFundModal(walletId, currency) {
            // Set values for both payment methods
            document.getElementById('modalWalletId').value = walletId;
            document.getElementById('modalWalletIdPaystack').value = walletId;
            document.getElementById('modalWalletCurrency').value = currency;
            document.getElementById('modalWalletCurrencyPaystack').value = currency;

            // Reset amount inputs
            document.getElementById('fundAmount').value = '';
            if (document.getElementById('fundAmountPaystack')) {
                document.getElementById('fundAmountPaystack').value = '';
            }

            // Show the modal
            document.getElementById('fundWalletModal').style.display = 'flex';

            // Show rate immediately for non-NGN wallets
            if (currency !== 'NGN') {
                const rateDisplay = document.getElementById('fundingRateDisplay');
                if (currency === 'USD') {
                    rateDisplay.textContent = `Exchange Rate: 1 USD = ${USD_RATE.toFixed(2)} NGN`;
                } else if (currency === 'GBP') {
                    rateDisplay.textContent = `Exchange Rate: 1 GBP = ${GBP_RATE.toFixed(2)} NGN`;
                }
                document.getElementById('fundingConversionPreview').style.display = 'block';
            }
        }

        function closeFundModal() {
            document.getElementById('fundWalletModal').style.display = 'none';
        }

        function updateFundingPreview() {
            const amount = parseFloat(document.getElementById('fundAmount').value) ||
                parseFloat(document.getElementById('fundAmountPaystack')?.value) || 0;
            const walletCurrency = document.getElementById('modalWalletCurrency').value;

            // Check for maximum amount
            if (amount > 500000) {
                showBankTransferMessage();
                return;
            }

            if (walletCurrency === 'NGN') {
                document.getElementById('fundingConversionPreview').style.display = 'none';
                return;
            }

            const rate = getConversionRate('NGN', walletCurrency);
            const convertedAmount = amount * rate;

            const rateDisplay = document.getElementById('fundingRateDisplay');
            const convertedAmountDisplay = document.getElementById('fundingConvertedAmountDisplay');

            // Display rate in USD/GBP -> NGN format
            if (walletCurrency === 'USD') {
                rateDisplay.textContent = `Exchange Rate: 1 USD = ${USD_RATE.toFixed(2)} NGN`;
            } else if (walletCurrency === 'GBP') {
                rateDisplay.textContent = `Exchange Rate: 1 GBP = ${GBP_RATE.toFixed(2)} NGN`;
            }

            // Only update converted amount if there is an amount
            if (amount > 0) {
                convertedAmountDisplay.textContent = `You will receive: ${formatCurrency(convertedAmount, walletCurrency)}`;
            } else {
                convertedAmountDisplay.textContent = '';
            }

            document.getElementById('fundingConversionPreview').style.display = 'block';
        }

        function showBankTransferMessage() {
            // Close the funding modal
            closeFundModal();

            // Create and show the bank transfer modal
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white p-8 rounded-lg max-w-md w-full">
                    <h2 class="text-2xl font-bold mb-4">Bank Transfer Required</h2>
                    <p class="mb-6 text-gray-600">For amounts above ₦500,000, please make a bank transfer and send evidence of payment to payments@billing.ad</p>
                    <button onclick="this.parentElement.parentElement.remove()" 
                        class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded">
                        Close
                    </button>
                </div>
            `;
            document.body.appendChild(modal);
        }

        function getConversionRate(fromCurrency, toCurrency) {
            if (fromCurrency === toCurrency) return 1;

            // When converting FROM NGN TO other currencies
            if (fromCurrency === 'NGN') {
                if (toCurrency === 'USD') return 1 / USD_RATE;
                if (toCurrency === 'GBP') return 1 / GBP_RATE;
            }

            // When converting TO NGN FROM other currencies
            if (toCurrency === 'NGN') {
                if (fromCurrency === 'USD') return USD_RATE;
                if (fromCurrency === 'GBP') return GBP_RATE;
            }

            // For USD to GBP and vice versa
            if (fromCurrency === 'USD' && toCurrency === 'GBP') return USD_RATE / GBP_RATE;
            if (fromCurrency === 'GBP' && toCurrency === 'USD') return GBP_RATE / USD_RATE;

            return 0;
        }

        function formatCurrency(amount, currency) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency,
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        }

        function viewReceipt(transactionId) {
            // Show loading state
            document.getElementById('receiptModal').style.display = 'flex';
            document.getElementById('receiptContent').innerHTML = '<p class="text-center">Loading...</p>';

            // Fetch receipt data
            fetch(`/wallet/transaction/${transactionId}/receipt`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('receiptContent').innerHTML = `
                        <div class="border-b pb-4">
                            <p class="text-sm text-gray-600">Transaction ID</p>
                            <p class="font-medium">${data.reference}</p>
                        </div>
                        <div class="border-b pb-4">
                            <p class="text-sm text-gray-600">Date</p>
                            <p class="font-medium">${data.date}</p>
                        </div>
                        <div class="border-b pb-4">
                            <p class="text-sm text-gray-600">Description</p>
                            <p class="font-medium">${data.description}</p>
                        </div>
                        <div class="border-b pb-4">
                            <p class="text-sm text-gray-600">Amount</p>
                            <p class="font-medium">${data.amount} ${data.currency}</p>
                        </div>
                        ${data.rate ? `
                                    <div class="border-b pb-4">
                                        <p class="text-sm text-gray-600">Exchange Rate</p>
                                        <p class="font-medium">1 ${data.source_currency} = ${data.rate} ${data.currency}</p>
                                    </div>
                                ` : ''}
                        <div class="border-b pb-4">
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="font-medium">${data.status}</p>
                        </div>
                    `;
                })
                .catch(error => {
                    document.getElementById('receiptContent').innerHTML =
                        '<p class="text-red-500 text-center">Failed to load receipt details</p>';
                });
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').style.display = 'none';
        }

        function openTransferModal() {
            // Show the modal
            document.getElementById('transferModal').style.display = 'flex';

            // Set initial currency flags and balance
            updateCurrencyFlags();
            updateSourceBalance();

            // Update preview to show rate immediately
            updateConversionPreview();
        }

        function updateCurrencyFlags() {
            const sourceCurrency = document.getElementById('transferSourceCurrency').value;
            const destCurrency = document.getElementById('transferDestinationCurrency').value;

            // Update source currency flag
            document.getElementById('sourceFlag').src = `/images/flags/${sourceCurrency.toLowerCase()}.png`;

            // Update destination currency flag
            document.getElementById('destFlag').src = `/images/flags/${destCurrency.toLowerCase()}.png`;
        }

        function updateSourceBalance() {
            const sourceSelect = document.getElementById('transferSourceCurrency');
            const selectedOption = sourceSelect.options[sourceSelect.selectedIndex];
            const balance = selectedOption.dataset.balance;

            document.getElementById('sourceBalance').textContent = formatCurrency(balance, sourceSelect.value);
        }

        function updateConversionPreview() {
            updateCurrencyFlags();
            updateSourceBalance();

            const amount = parseFloat(document.getElementById('transferAmount').value) || 0;
            const sourceCurrency = document.getElementById('transferSourceCurrency').value;
            const destCurrency = document.getElementById('transferDestinationCurrency').value;
            const rate = getConversionRate(sourceCurrency, destCurrency);
            const convertedAmount = amount * rate;

            const rateDisplay = document.getElementById('rateDisplay');
            const convertedAmountDisplay = document.getElementById('convertedAmountDisplay');

            if (sourceCurrency !== destCurrency) {
                // Always display rate, even when amount is 0
                if (sourceCurrency === 'USD' && destCurrency === 'NGN') {
                    rateDisplay.textContent = `Exchange Rate: 1 USD = ${USD_RATE.toFixed(2)} NGN`;
                } else if (sourceCurrency === 'GBP' && destCurrency === 'NGN') {
                    rateDisplay.textContent = `Exchange Rate: 1 GBP = ${GBP_RATE.toFixed(2)} NGN`;
                } else if (destCurrency === 'USD' && sourceCurrency === 'NGN') {
                    rateDisplay.textContent = `Exchange Rate: 1 USD = ${USD_RATE.toFixed(2)} NGN`;
                } else if (destCurrency === 'GBP' && sourceCurrency === 'NGN') {
                    rateDisplay.textContent = `Exchange Rate: 1 GBP = ${GBP_RATE.toFixed(2)} NGN`;
                } else {
                    rateDisplay.textContent = `Exchange Rate: 1 ${sourceCurrency} = ${rate.toFixed(4)} ${destCurrency}`;
                }

                // Only show converted amount when there is an amount
                convertedAmountDisplay.textContent = amount > 0 ?
                    `You will receive: ${formatCurrency(convertedAmount, destCurrency)}` : '';

                document.getElementById('conversionPreview').style.display = 'block';
            } else {
                document.getElementById('conversionPreview').style.display = 'none';
            }

            // Update the hidden wallet ID input
            const sourceSelect = document.getElementById('transferSourceCurrency');
            const selectedOption = sourceSelect.options[sourceSelect.selectedIndex];
            const walletId = selectedOption.dataset.walletId;

            // Create or update hidden input for wallet ID
            let walletIdInput = document.querySelector('input[name="source_wallet_id"]');
            if (!walletIdInput) {
                walletIdInput = document.createElement('input');
                walletIdInput.type = 'hidden';
                walletIdInput.name = 'source_wallet_id';
                document.querySelector('#transferModal form').appendChild(walletIdInput);
            }
            walletIdInput.value = walletId;
        }

        function closeTransferModal() {
            document.getElementById('transferModal').style.display = 'none';
        }
    </script>
</x-user-layout>
