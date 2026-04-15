<x-admin-layout page="settings">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Admin Settings</h1>

            {{-- Toast notification --}}
            <div id="rateRefreshToast"
                 style="display:none; position:fixed; top:20px; right:20px; z-index:9999;"
                 class="flex items-center gap-3 px-5 py-3 rounded-lg shadow-lg text-white text-sm font-medium transition-all">
            </div>

            <div class="bg-white shadow-md rounded my-6 p-6">
                <div class="mb-6">
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                        <p class="font-bold">Currency Rates Information</p>
                        <p>Currency rates (USD and GBP) are automatically updated twice daily using CurrencyFreaks API. You can adjust the margin below to add a fixed amount to the rates.</p>
                    </div>
                </div>

                <table class="min-w-max w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Name</th>
                            <th class="py-3 px-6 text-left">Value</th>
                            <th class="py-3 px-6 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach($settings as $setting)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $setting->name }}</td>
                                <td class="py-3 px-6 text-left">
                                    @if(in_array($setting->key, ['usd_rate', 'gbp_rate']))
                                        <span class="text-gray-800">{{ $setting->value }} NGN</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.adminsettings.update', $setting->id) }}" class="flex items-center">
                                            @csrf
                                            @method('POST')
                                            @if($setting->key === 'currency_margin')
                                                <input type="number"
                                                       name="value"
                                                       value="{{ $setting->value }}"
                                                       class="border rounded px-2 py-1 w-full"
                                                       step="0.01"
                                                       min="0">
                                            @else
                                                <input type="text"
                                                       name="value"
                                                       value="{{ $setting->value }}"
                                                       class="border rounded px-2 py-1 w-full">
                                            @endif
                                            <button type="submit"
                                                    class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                                Update
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    @if(!in_array($setting->key, ['usd_rate', 'gbp_rate', 'currency_margin']))
                                        <form method="POST" action="{{ route('admin.adminsettings.destroy', $setting->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded"
                                                    onclick="return confirm('Are you sure you want to delete this setting?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Current Rates Display -->
                <div class="mt-8 p-4 bg-gray-50 rounded">
                    <h2 class="text-lg font-semibold mb-4">Bloom Rates (with margin)</h2>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-600">USD to NGN Rate:</p>
                            <p class="font-medium">{{ number_format($settings->where('key', 'usd_rate')->first()?->value ?? 0, 2) }} NGN</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">GBP to NGN Rate:</p>
                            <p class="font-medium">{{ number_format($settings->where('key', 'gbp_rate')->first()?->value ?? 0, 2) }} NGN</p>
                        </div>
                    </div>

                    <h2 class="text-lg font-semibold mb-4">API Rates (CurrencyFreaks)</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">USD to NGN Rate:</p>
                            <p class="font-medium">{{ number_format(($settings->where('key', 'usd_rate')->first()?->value ?? 0) - ($settings->where('key', 'currency_margin')->first()?->value ?? 0), 2) }} NGN</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">GBP to NGN Rate:</p>
                            <p class="font-medium">{{ number_format(($settings->where('key', 'gbp_rate')->first()?->value ?? 0) - ($settings->where('key', 'currency_margin')->first()?->value ?? 0), 2) }} NGN</p>
                        </div>
                    </div>

                    <!-- Last update + Manual Refresh Button -->
                    <div class="flex items-center justify-between mt-4">
                        <p class="text-xs text-gray-500">
                            Rates are automatically updated twice daily. Last update:
                            {{ $settings->where('key', 'usd_rate')->first()?->updated_at?->diffForHumans() ?? 'Never' }}
                        </p>

                        @php
                            $refreshCacheKey = 'currency_refresh_count_' . now()->format('Y-m-d');
                            $usedToday      = (int) \Illuminate\Support\Facades\Cache::get($refreshCacheKey, 0);
                            $remaining      = max(0, 2 - $usedToday);
                        @endphp

                        <button id="refreshRatesBtn"
                                data-remaining="{{ $remaining }}"
                                data-url="{{ route('admin.adminsettings.refresh-rates') }}"
                                data-csrf="{{ csrf_token() }}"
                                {{ $remaining === 0 ? 'disabled' : '' }}
                                class="flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-lg border transition
                                       {{ $remaining === 0
                                            ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                            : 'bg-white text-blue-600 border-blue-300 hover:bg-blue-50 cursor-pointer' }}">
                            {{-- Refresh SVG icon --}}
                            <svg id="refreshIcon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span id="refreshBtnLabel">
                                {{ $remaining === 0 ? 'No refreshes left today' : 'Refresh Rates ('.$remaining.' left today)' }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Add New Setting Form -->
                <div class="mt-6 pt-6 border-t">
                    <h2 class="text-xl text-black pb-4">Add New Setting</h2>
                    <form method="POST" action="{{ route('admin.adminsettings.store') }}" class="flex gap-4">
                        @csrf
                        <div class="flex-1">
                            <input type="text"
                                   name="name"
                                   placeholder="Setting Name"
                                   class="border rounded px-2 py-1 w-full"
                                   required>
                        </div>
                        <div class="flex-1">
                            <input type="text"
                                   name="value"
                                   placeholder="Setting Value"
                                   class="border rounded px-2 py-1 w-full"
                                   required>
                        </div>
                        <button type="submit"
                                class="btn bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-4 rounded">
                            Add Setting
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        .spin { animation: spin 0.8s linear infinite; }
    </style>

    <script>
        (function () {
            const btn   = document.getElementById('refreshRatesBtn');
            const icon  = document.getElementById('refreshIcon');
            const label = document.getElementById('refreshBtnLabel');
            const toast = document.getElementById('rateRefreshToast');

            if (!btn) return;

            function showToast(message, success) {
                toast.textContent = message;
                toast.style.background = success ? '#16a34a' : '#dc2626';
                toast.style.display = 'flex';
                setTimeout(() => { toast.style.display = 'none'; }, 4000);
            }

            btn.addEventListener('click', function () {
                const remaining = parseInt(btn.dataset.remaining, 10);
                if (remaining <= 0) return;

                // Disable & spin while request is in-flight
                btn.disabled = true;
                icon.classList.add('spin');
                label.textContent = 'Refreshing…';

                fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': btn.dataset.csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                })
                .then(res => res.json())
                .then(data => {
                    icon.classList.remove('spin');

                    if (data.success) {
                        const newRemaining = data.remaining;
                        btn.dataset.remaining = newRemaining;

                        if (newRemaining <= 0) {
                            btn.disabled = true;
                            btn.className = btn.className
                                .replace('text-blue-600', 'text-gray-400')
                                .replace('border-blue-300', 'border-gray-200')
                                .replace('hover:bg-blue-50', '')
                                .replace('cursor-pointer', 'cursor-not-allowed');
                            label.textContent = 'No refreshes left today';
                        } else {
                            btn.disabled = false;
                            label.textContent = 'Refresh Rates (' + newRemaining + ' left today)';
                        }

                        showToast('✓ ' + data.message, true);

                        // Reload after a short delay so the page shows fresh rates
                        setTimeout(() => window.location.reload(), 1800);
                    } else {
                        btn.disabled = (parseInt(btn.dataset.remaining, 10) <= 0);
                        label.textContent = parseInt(btn.dataset.remaining, 10) > 0
                            ? 'Refresh Rates (' + btn.dataset.remaining + ' left today)'
                            : 'No refreshes left today';
                        showToast('✗ ' + data.message, false);
                    }
                })
                .catch(() => {
                    icon.classList.remove('spin');
                    btn.disabled = (parseInt(btn.dataset.remaining, 10) <= 0);
                    label.textContent = parseInt(btn.dataset.remaining, 10) > 0
                        ? 'Refresh Rates (' + btn.dataset.remaining + ' left today)'
                        : 'No refreshes left today';
                    showToast('✗ Network error. Please try again.', false);
                });
            });
        })();
    </script>
</x-admin-layout>

