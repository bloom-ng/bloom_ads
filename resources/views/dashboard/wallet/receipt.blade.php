<x-user-layout page="wallet">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg overflow-y-scroll">
                <!-- Receipt Header -->
                <div class="p-6 border-b">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Transaction Receipt</h1>
                            <p class="text-gray-600">{{ $organization->name }}</p>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('wallet.transaction.receipt.download', $transaction) }}"
                                class="inline-flex items-center px-4 py-2 btn rounded-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Receipt Content -->
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Transaction Reference</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $transaction->reference }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Date</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">
                                {{ $transaction->created_at->format('M d, Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Type</h3>
                            <p class="mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Description</h3>
                                <p class="mt-1 text-lg font-medium text-gray-900">{{ $transaction->description }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Amount</h3>
                                <p class="mt-1 text-lg font-medium text-gray-900">
                                    {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($transaction->rate && $transaction->source_currency)
                        <div class="border-t pt-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Exchange Rate</h3>
                                    <p class="mt-1 text-lg font-medium text-gray-900">
                                        1 {{ $transaction->source_currency }} =
                                        {{ number_format($transaction->rate, 4) }} {{ $transaction->currency }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-user-layout>
