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

                                    <div class="mt-4 space-y-2">
                                        @if ($wallet->currency === 'NGN')
                                            <!-- Fund Button - Only for NGN wallet -->
                                            <button
                                                onclick="openFundModal('{{ $wallet->id }}', '{{ $wallet->currency }}')"
                                                class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                                Fund Wallet
                                            </button>
                                        @endif

                                        <!-- Transfer Button -->
                                        <button
                                            onclick="openTransferModal('{{ $wallet->id }}', '{{ $wallet->currency }}', {{ $wallet->balance }})"
                                            class="w-full border border-[#F48857] text-[#F48857] hover:bg-[#F48857]/10 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            Transfer
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500">No wallets found.</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Fund Wallet Modal -->
    <div id="fundWalletModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Fund Wallet</h2>
            <p class="mb-4">Select your preferred payment method:</p>

            <div id="ngnPaymentOptions" class="space-y-4 mb-6">
                <form action="{{ route('wallet.fund.flutterwave') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="wallet_id" id="modalWalletId">
                    <input type="hidden" name="currency" id="modalCurrency">
                    <input type="number" name="amount" placeholder="Enter amount" required
                        class="w-full mb-4 p-2 border rounded">
                    <button type="submit"
                        class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded">
                        Pay with Flutterwave
                    </button>
                </form>

                <form action="{{ route('wallet.fund.paystack') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="wallet_id" id="modalWalletIdPaystack">
                    <input type="hidden" name="currency" id="modalCurrencyPaystack">
                    <input type="number" name="amount" placeholder="Enter amount" required
                        class="w-full mb-4 p-2 border rounded">
                    <button type="submit"
                        class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded">
                        Pay with Paystack
                    </button>
                </form>
            </div>

            <div id="otherPaymentOptions" class="space-y-4 mb-6">
                <form action="{{ route('wallet.fund.paypal') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="wallet_id" id="modalWalletIdPaypal">
                    <input type="hidden" name="currency" id="modalCurrencyPaypal">
                    <input type="number" name="amount" placeholder="Enter amount" required
                        class="w-full mb-4 p-2 border rounded">
                    <button type="submit"
                        class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded">
                        Pay with PayPal
                    </button>
                </form>
            </div>

            <button onclick="closeFundModal()"
                class="w-full mt-4 border border-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                Cancel
            </button>
        </div>
    </div>

    <!-- Add Transfer Modal -->
    <div id="transferModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Transfer Funds</h2>
            <form action="{{ route('wallet.transfer') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="source_wallet_id" id="transferSourceWalletId">
                <input type="hidden" name="source_currency" id="transferSourceCurrency">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" name="amount" id="transferAmount" required
                        class="w-full p-2 border rounded" step="0.01" min="0">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Destination Currency</label>
                    <select name="destination_currency" id="transferDestinationCurrency" required
                        class="w-full p-2 border rounded">
                        <option value="NGN">NGN</option>
                        <option value="USD">USD</option>
                        <option value="GBP">GBP</option>
                    </select>
                </div>

                <div id="conversionPreview" class="text-sm text-gray-600"></div>

                <button type="submit"
                    class="w-full bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded">
                    Transfer
                </button>
            </form>

            <button onclick="closeTransferModal()"
                class="w-full mt-4 border border-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                Cancel
            </button>
        </div>
    </div>

    <script>
        function openFundModal(walletId, currency) {
            document.getElementById('modalWalletId').value = walletId;
            document.getElementById('modalWalletIdPaypal').value = walletId;
            document.getElementById('modalWalletIdPaystack').value = walletId;
            document.getElementById('modalCurrency').value = currency;
            document.getElementById('modalCurrencyPaypal').value = currency;
            document.getElementById('modalCurrencyPaystack').value = currency;

            // Show/hide payment options based on currency
            if (currency === 'NGN') {
                document.getElementById('ngnPaymentOptions').style.display = 'block';
                document.getElementById('otherPaymentOptions').style.display = 'none';
            } else {
                document.getElementById('ngnPaymentOptions').style.display = 'none';
                document.getElementById('otherPaymentOptions').style.display = 'block';
            }

            document.getElementById('fundWalletModal').style.display = 'flex';
        }

        function closeFundModal() {
            document.getElementById('fundWalletModal').style.display = 'none';
        }

        function openTransferModal(walletId, currency, balance) {
            document.getElementById('transferSourceWalletId').value = walletId;
            document.getElementById('transferSourceCurrency').value = currency;
            document.getElementById('transferAmount').max = balance;

            // Remove current currency from destination options
            const destinationSelect = document.getElementById('transferDestinationCurrency');
            Array.from(destinationSelect.options).forEach(option => {
                option.disabled = option.value === currency;
            });

            document.getElementById('transferModal').style.display = 'flex';
        }

        function closeTransferModal() {
            document.getElementById('transferModal').style.display = 'none';
        }
    </script>
</x-user-layout>
