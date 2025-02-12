@props([
    'walletId' => null,
    'iconSrc' => null,
    'iconSvg' => null,
    'currency' => null,
    'balance' => null,
    'buttonText' => null,
    'buttonAction' => null,
])

<div class="card border rounded-xl shadow-sm p-6"
    @if ($walletId) data-wallet-id="{{ $walletId }}" @endif>
    @if ($iconSrc || $iconSvg)
        <div>
            @if ($iconSvg)
                {!! $iconSvg !!}
            @else
                <img class=" mb-1.5" src="{{ $iconSrc }}">
            @endif
        </div>
    @endif
    <div class="flex justify-between items-center">
        @if ($currency)
            <h3 class="text-xl font-bold">{{ $currency }}{{ $walletId ? ' Wallet' : '' }}</h3>
        @endif
        @if ($balance !== null)
            <span
                class="text-2xl font-bold">{{ is_numeric($balance) ? ($currency === 'Total Organizations' ? number_format($balance) : number_format($balance, 2)) : $balance }}</span>
        @endif
    </div>
    @if ($buttonText && $buttonAction)
        <div class="mt-4 space-y-2 flex justify-end">
            <button
                @if ($walletId) onclick="{{ $buttonAction }}('{{ $walletId }}', '{{ $currency }}')" @endif
                class="card-btn hover:bg-[#000080]/90 text-white text-xs font-bold py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">
                {{ $buttonText }}
            </button>
        </div>
    @endif
</div>
