<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
@php
    $currencySymbols = [
        'NGN' => '₦',
        'USD' => '$',
        'GBP' => '£',
    ];
    $currencySymbol = $currencySymbols[$transaction->currency] ?? $transaction->currency;
@endphp

<body class="min-h-screen bg-slate-100">
    <img class="h-[40vh] absolute -z-50 self-center w-full" src="/images/receipt_banner.png" />
    <div class="max-w-5xl mx-auto p-8">
        <!-- Header -->
        <div class="flex justify-between items-start text-white mb-12">
            <!-- Left Side -->
            <div>
                <div class="bg-white p-3 rounded-lg w-16 h-16 mb-6">
                    <img src="{{ asset('images/fav-icon3.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="text-5xl font-bold mb-2 text-[#000080]">RECEIPT</h1>
                <p class="text-xl mb-5 text-black">{{ $transaction->reference }}</p>
                <p class="text-lg text-black">{{ $transaction->created_at->format('F d, Y') }}</p>
            </div>

            <!-- Right Side -->
            <div class="text-right">
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-2 text-[#000080]">Billing</h2>
                    <p class="text-sm leading-relaxed text-black">
                        S03 Pathfield Mall,<br>
                        4th Avenue, Gwarimpa,<br>
                        Abuja, Nigeria.
                    </p>
                </div>
                <div>
                    <h2 class="text-xl font-semibold mb-2 text-[#000080]">Receipt to</h2>
                    <p class="text-sm leading-relaxed text-[#000080] font-semibold">
                        {{ $organization->name }}<br>
                    </p>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="bg-white rounded-t-3xl p-8 h-[70%] flex flex-col justify-between">
            <!-- Table -->
            <table class="w-full mb-8">
                <thead>
                    <tr class="text-[#000080]">
                        <th class="text-left py-4 font-bold">No.</th>
                        <th class="text-left py-4 font-bold">Item Description</th>
                        <th class="text-left py-4 font-bold">Unit Price ({{ $currencySymbol }})</th>
                        <th class="text-left py-4 font-bold">Qty.</th>
                        <th class="text-left py-4 font-bold">Total Price ({{ $currencySymbol }})</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-[#F6F6FF] border border-[#F6F6FF] rounded-lg p-2">
                        <td class="py-4 px-2 text-left">1</td>
                        <td class="py-4 px-2 text-left font-extrabold">
                            @php
                                $description = explode(' - ', $transaction->description);
                                $mainText = $description[0] ?? '';
                                $reference = $description[1] ?? '';
                            @endphp
                            {{ $mainText }}<br>
                            <span class="text-gray-600 text-xs font-extralight">{{ $reference }}</span>
                        </td>
                        <td class="py-4 px-2 text-left">
                            {{ number_format($transaction->amount, 2) }}</td>
                        <td class="py-4 px-2 text-left">1</td>
                        <td class="py-4 px-2 text-left">
                            {{ number_format($transaction->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="grid grid-cols-2">
                <div></div>
                <div class="flex flex-col items-center space-y-3 mb-12 mt-4 self-end">
                    <div class="flex justify-between w-[80%]">
                        <span class="font-bold">Sub Total</span>
                        <span
                            class="text-[#000080] font-semibold">{{ $currencySymbol }}{{ number_format($subTotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between w-[80%]">
                        <span class="font-bold">Tax</span>
                        <span
                            class="text-[#000080] font-semibold">{{ $currencySymbol }}{{ number_format($vat, 2) }}</span>
                    </div>
                    <div class="flex justify-between w-[80%]">
                        <span class="font-bold">Service Charge</span>
                        <span
                            class="text-[#000080] font-semibold">{{ $currencySymbol }}{{ number_format($serviceFee, 2) }}</span>
                    </div>
                    <div
                        class="text-black px-10 py-3 rounded-xl mt-4 w-full font-bold flex justify-between bg-[#F6F6FF]">
                        Total: <span
                            class="font-semibold">{{ $currencySymbol }}{{ number_format($transaction->amount, 2) }}</span>
                    </div>

                    {{-- <p class="text-sm text-gray-600 w-[80%] self-center">
                        Kindly pay to the account number provided in this invoice. If for any reason a refund is
                        requested,
                        administrative charge will be deducted before refund is made
                    </p> --}}
                </div>
            </div>

            <div class="border-t pt-8 grid grid-cols-2">
                <div
                    class="flex justify-between w-[80%] bg-gradient-to-r from-[#181898] to-[#5151ae] text-white px-5 py-3 rounded-xl">
                    <span class="font-bold">Amount Paid</span>
                    <span
                        class="font-semibold">{{ $currencySymbol }}{{ number_format($transaction->amount, 2) }}</span>
                </div>
                <div></div>
            </div>
            <div class="mt-2 flex flex-row justify-between">
                <p class="font-bold mb-6">Thank you for doing business with us!</p>

                <div class="flex items-center gap-8 mb-6 text-sm font-extralight">
                    <div class="flex items-center">
                        <svg class="text-[#000080] w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                        +234 708 627 8644
                    </div>
                    <div class="flex items-center">
                        <svg class="text-[#000080] w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" />
                        </svg>
                        billing.ad
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Button - Only visible on screen -->
        <div class="mt-8 flex justify-between print:hidden">
            <button onclick="window.location.href='/dashboard'"
                class="bg-white text-[#27237C] px-6 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                Goto dashboard
            </button>
            <button onclick="window.print()"
                class="bg-white text-[#27237C] px-6 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                Print Receipt
            </button>
        </div>
    </div>
</body>

</html>
