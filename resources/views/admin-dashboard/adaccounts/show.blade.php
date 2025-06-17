<x-admin-layout page="organizations">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl text-black">Ad Account Details</h1>
                <a href="{{ route('admin.organizations.show', $adAccount->organization_id) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Organization Ad accounts
                </a>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4 mb-6">
                <button onclick="openFundModal()"
                    class="bg-[#F48857] text-black px-6 py-3 rounded-md hover:bg-[#F48857]/80 font-medium">
                    Fund Ad Account
                </button>
                <button onclick="openWithdrawModal()"
                    class="bg-white text-black border border-[#F48857] px-6 py-3 rounded-md hover:bg-gray-50 font-medium">
                    Withdraw Funds
                </button>
            </div>

            <!-- Ad Account Details -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Balance</p>
                        <p class="font-medium text-xl">{{ $adAccount->currency }}
                            {{ number_format($adAccount->getBalance(), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Name</p>
                        <p class="font-medium">{{ $adAccount->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status</p>
                        @if ($providerInfo['_provider'] == 'meta')
                            <p class="font-medium">
                                <span
                                    class="px-2 py-1 rounded-full text-sm
                                {{ $adAccount->status === 'approved'
                                    ? 'bg-green-100 text-green-800'
                                    : ($adAccount->status === 'processing'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-red-100 text-red-800') }}">
                                    {{ \App\Services\Meta\Data\AdAccountMapping::accountStatusMapping()[$providerInfo['_meta_ad_account']['account_status']] }}
                                </span>
                            </p>
                        @else
                            <p class="font-medium">
                                <span
                                    class="px-2 py-1 rounded-full text-sm
                                {{ $adAccount->status === 'approved'
                                    ? 'bg-green-100 text-green-800'
                                    : ($adAccount->status === 'processing'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($adAccount->status) }}
                                </span>
                            </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-gray-600">Type</p>
                        <p class="font-medium">{{ ucfirst($adAccount->type) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Currency</p>
                        <p class="font-medium">{{ $adAccount->currency }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Organization</p>
                        <p class="font-medium">{{ $adAccount->organization->name }}</p>
                    </div>
                    @if ($providerInfo['_provider'] == 'meta')
                        <div>
                            <p class="text-gray-600">Meta Account</p>
                            <p class="font-medium"> {{ $providerInfo['_meta_ad_account']['name'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Amount Spent</p>
                            <p class="font-medium"> {{ $providerInfo['_meta_ad_account']['currency'] }}
                                {{ $providerInfo['_meta_ad_account']['amount_spent'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Remaining Balance</p>
                            <p class="font-medium"> {{ $providerInfo['_meta_ad_account']['currency'] }}
                                {{ abs(round(($providerInfo['_meta_ad_account']['spend_cap'] - $providerInfo['_meta_ad_account']['amount_spent']) / 100, 2)) }}
                            </p>
                        </div>
                    @endif
                    <div>
                        <p class="text-gray-600">Created By</p>
                        <p class="font-medium">{{ $adAccount->user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Fund Modal -->
            <div id="fundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg w-full max-w-md">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-medium">Fund Ad Account</h3>
                        <button onclick="closeFundModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.adaccounts.fund', $adAccount->id) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Organization
                                    Wallet</label>
                                <select name="wallet_id" id="fundWalletId" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    onchange="updateFundPreview()">
                                    @foreach ($adAccount->organization->wallets->where('currency', $adAccount->currency) as $wallet)
                                        <option value="{{ $wallet->id }}">
                                            {{ $wallet->currency }} Wallet (Balance:
                                            {{ number_format($wallet->getBalance(), 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount</label>
                                <input type="number" name="amount" id="fundAmount" step="0.01" min="0.01"
                                    required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    onchange="updateFundPreview()">
                            </div>

                            <!-- Preview Section -->
                            <div class="bg-gray-50 p-4 rounded-md">
                                <h4 class="font-medium mb-2">Transaction Preview</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Amount:</span>
                                        <span id="previewFundAmount">0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>VAT ({{ $adAccount->getVatRate() }}%):</span>
                                        <span id="previewFundVat">0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Service Fee ({{ $adAccount->getServiceFeeRate() }}%):</span>
                                        <span id="previewFundServiceFee">0.00</span>
                                    </div>
                                    <div class="flex justify-between font-medium">
                                        <span>Total:</span>
                                        <span id="previewFundTotal">0.00</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-[#F48857] text-black px-4 py-2 rounded-md hover:bg-[#F48857]/80">
                                Confirm Funding
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Withdraw Modal -->
            <div id="withdrawModal"
                class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg w-full max-w-md">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-medium">Withdraw from Ad Account</h3>
                        <button onclick="closeWithdrawModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.adaccounts.withdraw', $adAccount->id) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Organization
                                    Wallet</label>
                                <select name="wallet_id" id="withdrawWalletId" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    onchange="updateWithdrawPreview()">
                                    @foreach ($adAccount->organization->wallets->where('currency', $adAccount->currency) as $wallet)
                                        <option value="{{ $wallet->id }}">
                                            {{ $wallet->currency }} Wallet (Balance:
                                            {{ number_format($wallet->getBalance(), 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount</label>
                                <input type="number" name="amount" id="withdrawAmount" step="0.01"
                                    min="0.01" max="{{ $adAccount->getBalance() }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    onchange="updateWithdrawPreview()">
                            </div>

                            <!-- Preview Section -->
                            <div class="bg-gray-50 p-4 rounded-md">
                                <h4 class="font-medium mb-2">Transaction Preview</h4>
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
                                    <div class="flex justify-between font-medium">
                                        <span>Total:</span>
                                        <span id="previewWithdrawTotal">0.00</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-[#F48857] text-black px-4 py-2 rounded-md hover:bg-[#F48857]/80">
                                Confirm Withdrawal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Add JavaScript for modal and calculations -->
            <script>
                function openFundModal() {
                    document.getElementById('fundModal').classList.remove('hidden');
                    document.getElementById('fundModal').classList.add('flex');
                }

                function closeFundModal() {
                    document.getElementById('fundModal').classList.add('hidden');
                    document.getElementById('fundModal').classList.remove('flex');
                }

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

                function updateFundPreview() {
                    const amount = parseFloat(document.getElementById('fundAmount').value) || 0;
                    const fees = calculateFees(amount);

                    document.getElementById('previewFundAmount').textContent = fees.amount.toFixed(2);
                    document.getElementById('previewFundVat').textContent = fees.vat.toFixed(2);
                    document.getElementById('previewFundServiceFee').textContent = fees.serviceFee.toFixed(2);
                    document.getElementById('previewFundTotal').textContent = fees.total.toFixed(2);
                }

                function openWithdrawModal() {
                    document.getElementById('withdrawModal').classList.remove('hidden');
                    document.getElementById('withdrawModal').classList.add('flex');
                }

                function closeWithdrawModal() {
                    document.getElementById('withdrawModal').classList.add('hidden');
                    document.getElementById('withdrawModal').classList.remove('flex');
                }

                function updateWithdrawPreview() {
                    const amount = parseFloat(document.getElementById('withdrawAmount').value) || 0;
                    const fees = calculateFees(amount);

                    document.getElementById('previewWithdrawAmount').textContent = fees.amount.toFixed(2);
                    document.getElementById('previewWithdrawVat').textContent = fees.vat.toFixed(2);
                    document.getElementById('previewWithdrawServiceFee').textContent = fees.serviceFee.toFixed(2);
                    document.getElementById('previewWithdrawTotal').textContent = fees.total.toFixed(2);
                }

                // Close withdraw modal when clicking outside
                window.onclick = function(event) {
                    if (event.target == document.getElementById('withdrawModal')) {
                        closeWithdrawModal();
                    } else if (event.target == document.getElementById('fundModal')) {
                        closeFundModal();
                    }
                }
            </script>

            <!-- Rest of the ad account details -->
        </main>
    </div>
</x-admin-layout>
