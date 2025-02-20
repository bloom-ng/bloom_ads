<x-user-layout page="adaccounts">
    <div class="dashboard text container mx-auto px-6 py-8">
        <div class="flex justify-between items-center">
            <h3 class="text text-3xl font-medium">Edit Ad Account</h3>
            <a href="{{ route('adaccounts.index') }}" class="px-4 py-2 btn-primary rounded-md">
                ← Back to Ad Accounts
            </a>
        </div>

        <div class="mt-8">
            <form action="{{ route('adaccounts.update', $adAccount->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text">Name</label>
                    <input type="text" name="name" value="{{ old('name', $adAccount->name) }}"
                        class="mt-1 block w-full rounded-md forms" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text">Type</label>
                    <select name="type" class="mt-1 block w-full rounded-md forms" required>
                        <option value="meta" {{ $adAccount->type === 'meta' ? 'selected' : '' }}>Meta</option>
                        <option value="google" {{ $adAccount->type === 'google' ? 'selected' : '' }}>Google</option>
                        <option value="tiktok" {{ $adAccount->type === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text">Currency</label>
                    <select name="currency" class="mt-1 block w-full rounded-md forms" required>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency }}"
                                {{ $adAccount->currency === $currency ? 'selected' : '' }}>
                                {{ $currency }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text">Business Manager ID</label>
                    <input type="text" name="business_manager_id"
                        value="{{ old('business_manager_id', $adAccount->business_manager_id) }}"
                        class="mt-1 block w-full rounded-md forms">
                </div>

                <div>
                    <label class="block text-sm font-medium text">Landing Page</label>
                    <input type="url" name="landing_page"
                        value="{{ old('landing_page', $adAccount->landing_page) }}"
                        class="mt-1 block w-full rounded-md forms">
                </div>

                <div>
                    <label class="block text-sm font-medium text">Page URL</label>
                    <input type="url" name="facebook_page_url"
                        value="{{ old('facebook_page_url', $adAccount->facebook_page_url) }}"
                        class="mt-1 block w-full rounded-md forms"
                        placeholder="https://your-page" required>
                    @error('facebook_page_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="px-4 py-2 btn rounded-md">
                        Update Ad Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-user-layout>
