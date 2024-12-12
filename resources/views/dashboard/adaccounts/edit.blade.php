<x-user-layout page="adaccounts">
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center">
            <h3 class="text-gray-700 text-3xl font-medium">Edit Ad Account</h3>
            <a href="{{ route('adaccounts.index') }}" 
               class="text-[#F48857] hover:text-[#F48857]/80">
                ← Back to Ad Accounts
            </a>
        </div>

        <div class="mt-8">
            <form action="{{ route('adaccounts.update', $adAccount->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $adAccount->name) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="meta" {{ $adAccount->type === 'meta' ? 'selected' : '' }}>Meta</option>
                        <option value="google" {{ $adAccount->type === 'google' ? 'selected' : '' }}>Google</option>
                        <option value="tiktok" {{ $adAccount->type === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Currency</label>
                    <select name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency }}" {{ $adAccount->currency === $currency ? 'selected' : '' }}>
                                {{ $currency }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Business Manager ID</label>
                    <input type="text" 
                           name="business_manager_id" 
                           value="{{ old('business_manager_id', $adAccount->business_manager_id) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Landing Page</label>
                    <input type="url" 
                           name="landing_page" 
                           value="{{ old('landing_page', $adAccount->landing_page) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <button type="submit" 
                            class="px-4 py-2 bg-[#F48857] text-white rounded-md hover:bg-[#F48857]/80">
                        Update Ad Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-user-layout> 