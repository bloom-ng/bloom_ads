<x-admin-layout page="settings">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Admin Settings</h1>

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
                            @if($setting->key === 'currency_margin')
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $setting->name }}</td>
                                    <td class="py-3 px-6 text-left">
                                        <form method="POST" action="{{ route('admin.adminsettings.update', $setting->id) }}" class="flex items-center">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" 
                                                   name="value" 
                                                   value="{{ $setting->value }}" 
                                                   class="border rounded px-2 py-1 w-full"
                                                   step="0.01"
                                                   min="0">
                                            <button type="submit" 
                                                    class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <!-- No delete button for margin setting -->
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>

                <!-- Current Rates Display -->
                <div class="mt-8 p-4 bg-gray-50 rounded">
                    <h2 class="text-lg font-semibold mb-4">Current Exchange Rates</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">USD to NGN Rate:</p>
                            <p class="font-medium">{{ number_format($settings->where('key', 'usd_rate')->first()?->value ?? 0, 2) }} NGN</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">GBP to NGN Rate:</p>
                            <p class="font-medium">{{ number_format($settings->where('key', 'gbp_rate')->first()?->value ?? 0, 2) }} NGN</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Rates are automatically updated twice daily. Last update: {{ $settings->where('key', 'usd_rate')->first()?->updated_at?->diffForHumans() ?? 'Never' }}</p>
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
        </main>
    </div>
</x-admin-layout>
