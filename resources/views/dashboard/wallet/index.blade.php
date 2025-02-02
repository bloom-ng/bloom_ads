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
                            <div class="flex gap-3 mb-8 mt-2">
                                <!-- Add Global Convert Button -->
                                <div class="">
                                    <button onclick="openCreateWalletModal()"
                                        class="text-[#000080] font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline border border-[#000080]">
                                        Create New Wallet
                                    </button>
                                </div>

                                <div class="">
                                    <button onclick="openTransferModal()"
                                        class="text-[#000080] font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline border border-[#000080]">
                                        Convert Currency
                                    </button>
                                </div>

                                <div class="">
                                    <button onclick="openWithdrawModal()"
                                        class="text-[#000080] font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline border border-[#000080]">
                                        Withdraw Funds
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Existing Wallets -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse ($organization->wallets as $wallet)
                                <x-card :wallet-id="$wallet->id" :icon-src="asset($wallet->currency === 'NGN' ? 'images/naira_icon.svg' : ($wallet->currency === 'USD' ? 'images/usd_icon.svg' : 'images/gbp_icon.svg'))" :currency="$wallet->currency" :balance="$wallet->getBalance()"
                                    :button-text="'Fund Wallet'" :button-action="'openFundModal'" />
                            @empty
                                <p class="text-gray-500">No wallets found.</p>
                            @endforelse
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
    <div id="fundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-medium">Fund Wallet</h3>
                <button onclick="closeFundModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount</label>
                    <input type="number" id="fundAmount" step="0.01" min="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm px-4 py-2"
                        placeholder="Enter amount" oninput="updateFundingPreview()">

                    <!-- Add the conversion preview div -->
                    <div id="fundingConversionPreview" class="text-sm text-gray-600 bg-gray-50 p-3 rounded mt-2"
                        style="display: none;">
                        <p id="fundingRateDisplay" class="mb-2"></p>
                        <p id="fundingConvertedAmountDisplay"></p>
                    </div>
                </div>

                <!-- Payment Method Buttons -->
                <div class="flex flex-col gap-4">
                    <!-- Payment Buttons Container -->
                    <div class="payment-buttons">
                        <!-- Flutterwave Button -->
                        <form action="{{ route('wallet.fund.flutterwave') }}" method="POST" id="flutterwaveForm">
                            @csrf
                            <input type="hidden" name="wallet_id" id="flutterwaveWalletId">
                            <input type="hidden" name="amount" id="flutterwaveAmount">
                            <input type="hidden" name="currency" value="NGN">
                            <input type="hidden" name="wallet_currency" id="flutterwaveWalletCurrency">
                            <button type="button" onclick="submitFlutterwavePayment()"
                                class="w-full px-4 py-2 btn rounded-md mb-2">
                                Pay with Flutterwave
                            </button>
                        </form>

                        {{-- <!-- Paystack Button -->
                        <form action="{{ route('wallet.fund.paystack') }}" method="POST" id="paystackForm">
                            @csrf
                            <input type="hidden" name="wallet_id" id="paystackWalletId">
                            <input type="hidden" name="amount" id="paystackAmount">
                            <input type="hidden" name="currency" value="NGN">
                            <input type="hidden" name="wallet_currency" id="paystackWalletCurrency">
                            <button type="button" onclick="submitPaystackPayment()"
                                class="w-full bg-[#0BA4DB] hover:bg-[#0BA4DB]/90 text-black font-bold py-2 px-4 rounded">
                                Pay with Paystack
                            </button>
                        </form> --}}
                    </div>

                    <!-- Message for foreign currency high amounts -->
                    <div id="foreignCurrencyMessage" style="display: none;"
                        class="text-center p-4 bg-gray-50 rounded-lg mt-4">
                    </div>

                    <!-- Generate Invoice Button -->
                    <div class="generate-invoice-button" style="display: none;">
                        <form action="{{ route('wallet.generate.invoice') }}" method="POST">
                            @csrf
                            <input type="hidden" name="wallet_id" id="invoiceWalletId">
                            <input type="hidden" name="amount" id="invoiceAmount">
                            <input type="hidden" name="currency" id="invoiceCurrency">
                            <button type="submit" class="w-full px-4 py-2 btn-primary rounded-md font-bold">
                                Generate Invoice
                            </button>
                        </form>
                    </div>
                </div>
            </div>
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

                <button type="submit" class="w-full px-4 py-2 btn-primary rounded-md">
                    Convert
                </button>
            </form>

            <button onclick="closeTransferModal()" class="w-full mt-4 px-4 py-2 btn rounded-md">
                Cancel
            </button>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div id="withdrawModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-auto">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Withdraw Funds</h2>
            <form action="{{ route('wallet.withdraw') }}" method="POST" class="space-y-4" id="withdrawForm">
                @csrf
                <input type="hidden" name="wallet_id" id="withdrawWalletId">

                <div class="w-full">
                    <label for="bank_code" class="block text-sm font-medium text-gray-700 mb-2">Select Bank</label>
                    <select name="bank_code" id="bank_code" required
                        class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        <option value="">Loading banks...</option>
                    </select>
                </div>

                <div>
                    <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">Account
                        Number</label>
                    <input type="text" name="account_number" id="account_number" required
                        class="w-full rounded-md border-gray-300 shadow-sm py-2 px-3 sm:text-sm"
                        placeholder="Enter account number">
                    <p class="mt-1 text-sm text-gray-500" id="account_name_display"></p>
                </div>

                <div>
                    <label for="withdrawAmount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="number" name="amount" id="withdrawAmount" required min="100"
                            step="0.01" class="w-full rounded-md border-gray-300 shadow-sm py-2 px-3 sm:text-sm"
                            placeholder="Enter amount">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Available balance: <span
                            id="withdrawAvailableBalance">0.00</span></p>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeWithdrawModal()" class="px-4 py-2 btn-primary rounded-md">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 btn rounded-md">
                        Withdraw
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center overflow-auto">
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

    <!-- Create Wallet Modal -->
    <div id="createWalletModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-medium">Create New Wallet</h3>
                <button onclick="closeCreateWalletModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('wallet.create') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="organization_id" value="{{ $organization->id }}">

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Currency
                    </label>
                    <select name="currency" id="currency" required
                        class="w-full rounded-md border border-[#000080] shadow-sm sm:text-sm px-3 py-3">
                        <option value="NGN">NGN</option>
                        <option value="USD">USD</option>
                        <option value="GBP">GBP</option>
                    </select>
                </div>

                <div class="flex flex-col space-y-3 mt-6">
                    <button type="button" onclick="closeCreateWalletModal()"
                        class="px-4 py-2 rounded-md text-[#000080] border border-[#000080]">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-md bg-[#000080] text-white">
                        Create Wallet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('#bank_code').select2({
                width: '100%'
            });
        });

        const USD_RATE = {{ \App\Models\Wallet::getRate('usd') }};
        const GBP_RATE = {{ \App\Models\Wallet::getRate('gbp') }};

        function submitFlutterwavePayment() {
            const amount = document.getElementById('fundAmount').value;
            if (!amount || amount <= 0) {
                alert('Please enter a valid amount');
                return;
            }
            document.getElementById('flutterwaveAmount').value = amount;
            document.getElementById('flutterwaveForm').submit();
        }

        // function submitPaystackPayment() {
        //     const amount = document.getElementById('fundAmount').value;
        //     if (!amount || amount <= 0) {
        //         alert('Please enter a valid amount');
        //         return;
        //     }
        //     document.getElementById('paystackAmount').value = amount;
        //     document.getElementById('paystackForm').submit();
        // }

        function openFundModal(walletId, currency) {
            // Reset the amount input
            document.getElementById('fundAmount').value = '';

            // Update Flutterwave form fields
            document.getElementById('flutterwaveWalletId').value = walletId;
            document.getElementById('flutterwaveWalletCurrency').value = currency;

            // // Update Paystack form fields
            // document.getElementById('paystackWalletId').value = walletId;
            // document.getElementById('paystackWalletCurrency').value = currency;

            // Update Invoice form fields
            document.getElementById('invoiceWalletId').value = walletId;
            document.getElementById('invoiceCurrency').value = currency;

            // Show the modal
            document.getElementById('fundModal').style.display = 'flex';

            // Show initial rate for non-NGN wallets
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
            document.getElementById('fundModal').style.display = 'none';
        }

        function updateFundingPreview() {
            const amount = parseFloat(document.getElementById('fundAmount').value) || 0;
            const currency = document.getElementById('flutterwaveWalletCurrency').value;

            // Get payment buttons container
            const paymentButtons = document.querySelector('.payment-buttons');
            const generateInvoiceButton = document.querySelector('.generate-invoice-button');

            if (amount > 500000) {
                if (currency === 'NGN') {
                    // Hide payment buttons and show generate invoice button
                    paymentButtons.style.display = 'none';
                    generateInvoiceButton.style.display = 'block';
                    // Update invoice amount
                    document.getElementById('invoiceAmount').value = amount;
                } else {
                    // Hide payment buttons and show message for USD/GBP
                    paymentButtons.style.display = 'none';
                    generateInvoiceButton.style.display = 'none';

                    // Create or update the message div
                    let messageDiv = document.getElementById('foreignCurrencyMessage');
                    if (!messageDiv) {
                        messageDiv = document.createElement('div');
                        messageDiv.id = 'foreignCurrencyMessage';
                        messageDiv.className = 'text-center p-4 bg-gray-50 rounded-lg mt-4';
                        document.querySelector('.payment-buttons').insertAdjacentElement('afterend', messageDiv);
                    }
                    messageDiv.innerHTML = `
                        <p class="text-gray-600">For amounts above 500,000 NGN, please use the NGN wallet to generate an invoice.</p>
                    `;
                    messageDiv.style.display = 'block';
                }
            } else {
                // Show payment buttons and hide generate invoice button
                paymentButtons.style.display = 'block';
                generateInvoiceButton.style.display = 'none';
                // Hide the message if it exists
                const messageDiv = document.getElementById('foreignCurrencyMessage');
                if (messageDiv) {
                    messageDiv.style.display = 'none';
                }
                // Update payment form amounts
                document.getElementById('flutterwaveAmount').value = amount;
                // document.getElementById('paystackAmount').value = amount;
            }

            if (currency === 'NGN') {
                document.getElementById('fundingConversionPreview').style.display = 'none';
                return;
            }

            const rate = getConversionRate('NGN', currency);
            const convertedAmount = amount * rate;

            const rateDisplay = document.getElementById('fundingRateDisplay');
            const convertedAmountDisplay = document.getElementById('fundingConvertedAmountDisplay');

            // Display rate in USD/GBP -> NGN format
            if (currency === 'USD') {
                rateDisplay.textContent = `Exchange Rate: 1 USD = ${USD_RATE.toFixed(2)} NGN`;
            } else if (currency === 'GBP') {
                rateDisplay.textContent = `Exchange Rate: 1 GBP = ${GBP_RATE.toFixed(2)} NGN`;
            }

            // Only update converted amount if there is an amount
            if (amount > 0) {
                convertedAmountDisplay.textContent = `You will receive: ${formatCurrency(convertedAmount, currency)}`;
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

        async function loadBanks() {
            try {
                const response = await fetch('{{ route('wallet.banks') }}');
                const data = await response.json();

                if (data.data) {
                    const bankSelect = document.getElementById('bank_code');
                    bankSelect.innerHTML = '<option value="">Select Bank</option>';

                    data.data.forEach(bank => {
                        const option = document.createElement('option');
                        option.value = bank.code;
                        option.textContent = bank.name;
                        bankSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Failed to load banks:', error);
            }
        }

        function openWithdrawModal() {
            // Find the NGN wallet from the organization's wallets
            const ngnWallet = @json($organization->wallets->where('currency', 'NGN')->first());

            if (!ngnWallet) {
                alert('NGN wallet is required for withdrawals');
                return;
            }

            // Set the wallet ID and display the balance
            document.getElementById('withdrawWalletId').value = ngnWallet.id;
            document.getElementById('withdrawAvailableBalance').textContent =
                `NGN ${parseFloat(ngnWallet.calculated_balance).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

            // Show the modal
            const modal = document.getElementById('withdrawModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Load banks
            loadBanks();
        }

        function closeWithdrawModal() {
            const modal = document.getElementById('withdrawModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.getElementById('withdrawForm').reset();
        }

        let verifyAccountTimeout;

        async function verifyBankAccount() {
            const accountNumber = document.getElementById('account_number').value;
            const bankCode = document.getElementById('bank_code').value;
            const accountNameDisplay = document.getElementById('account_name_display');

            if (!accountNumber || !bankCode || accountNumber.length < 10) {
                accountNameDisplay.textContent = '';
                return;
            }

            try {
                accountNameDisplay.textContent = 'Verifying account...';

                const response = await fetch('{{ route('wallet.verify.account') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        account_number: accountNumber,
                        bank_code: bankCode
                    })
                });

                const data = await response.json();
                console.log('Verification response:', data);

                if (data.status === 'success' && data.data) {
                    accountNameDisplay.textContent = data.data.account_name;
                    accountNameDisplay.classList.remove('text-red-500');
                    accountNameDisplay.classList.add('text-green-500');
                } else {
                    accountNameDisplay.textContent = data.message || 'Could not verify account';
                    accountNameDisplay.classList.remove('text-green-500');
                    accountNameDisplay.classList.add('text-red-500');
                }
            } catch (error) {
                console.error('Error:', error);
                accountNameDisplay.textContent = 'Error verifying account';
                accountNameDisplay.classList.remove('text-green-500');
                accountNameDisplay.classList.add('text-red-500');
            }
        }

        // Add event listeners for account verification
        document.getElementById('account_number').addEventListener('input', function() {
            clearTimeout(verifyAccountTimeout);
            verifyAccountTimeout = setTimeout(verifyBankAccount, 500);
        });

        document.getElementById('bank_code').addEventListener('change', function() {
            const accountNumber = document.getElementById('account_number').value;
            if (accountNumber) {
                verifyBankAccount();
            }
        });

        document.getElementById('withdrawForm').addEventListener('submit', function(e) {
            const amount = parseFloat(document.getElementById('withdrawAmount').value);
            const balance = parseFloat(document.getElementById('withdrawAvailableBalance').textContent.replace(/,/g,
                ''));

            if (amount > balance) {
                e.preventDefault();
                alert('Withdrawal amount cannot exceed available balance');
            }
        });

        function openCreateWalletModal() {
            document.getElementById('createWalletModal').classList.remove('hidden');
            document.getElementById('createWalletModal').classList.add('flex');
        }

        function closeCreateWalletModal() {
            document.getElementById('createWalletModal').classList.remove('flex');
            document.getElementById('createWalletModal').classList.add('hidden');
        }
    </script>
</x-user-layout>
