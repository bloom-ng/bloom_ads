<x-user-layout page="wallet">
    <div class="max-w-4xl mx-auto my-8 bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Invoice Header -->
        <div class="p-6 border-b">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-600">Billing</p>
                    <h1 class="text-2xl font-bold text-gray-800">INVOICE</h1>
                    <p class="text-gray-600">{{ $organization->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-600">Invoice #: {{ $invoiceNumber }}</p>
                    <p class="text-gray-600">Date: {{ now()->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>

        <!-- Bill To Section -->
        <div class="p-6 border-b">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h2 class="font-semibold text-gray-700 mb-2">Bill To:</h2>
                    <p class="text-gray-600">{{ $organization->name }}</p>
                    {{-- <p class="text-gray-600">{{ $organization->address ?? 'N/A' }}</p> --}}
                </div>
                <div>
                    <h2 class="font-semibold text-gray-700 mb-2">Pay To:</h2>
                    <p class="text-gray-600">{{ $accountName }}</p>
                    <p class="text-gray-600">Bank: {{ $bankName }}</p>
                    <p class="text-gray-600">Account: {{ $accountNumber }}</p>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="p-6">
            <table class="w-full mb-6">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3">Description</th>
                        <th class="text-right py-3">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-3">Wallet Funding</td>
                        <td class="text-right">{{ number_format($amount, 2) }} {{ $currency }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3">VAT ({{ $vatRate }}%)</td>
                        <td class="text-right">{{ number_format($vat, 2) }} {{ $currency }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3">Service Fee ({{ $serviceFeeRate }}%)</td>
                        <td class="text-right">{{ number_format($serviceFee, 2) }} {{ $currency }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-bold">
                        <td class="py-3">Total</td>
                        <td class="text-right">{{ number_format($total, 2) }} {{ $currency }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Download Button -->
            <div class="flex justify-end mt-6">
                <a href="{{ route('wallet.invoice.download', ['amount' => $amount, 'currency' => $currency]) }}"
                    class="px-4 py-2 btn rounded-md inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download Invoice
                </a>
            </div>
        </div>
    </div>
</x-user-layout>
