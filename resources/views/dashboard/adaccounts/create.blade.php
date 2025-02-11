<x-user-layout page="adaccounts">
    <div class="container mx-auto px-6 py-8 h-full overflow-y-auto">
        <h3 class="text-gray-700 text-3xl font-medium">Create Ad Account</h3>

        <div class="mt-8 mb-8 bg-white rounded-lg shadow">
            <form action="{{ route('adaccounts.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div>
                    <label class="text-gray-700" for="name">Account Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="form-input mt-1 block w-full rounded-md border-black" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700" for="type">Platform Type</label>
                    <select name="type" id="type"
                        class="form-select mt-1 block w-full rounded-md border-gray-300" required>
                        <option value="">Select Platform</option>
                        @foreach ($platforms as $platform)
                            <option value="{{ $platform['id'] }}"
                                {{ old('type') == $platform['id'] ? 'selected' : '' }}>
                                {{ $platform['name'] }} ({{ $platform['id'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700" for="timezone">Timezone</label>
                    <select name="timezone" id="timezone"
                        class="form-select mt-1 block w-full rounded-md border-gray-300" required>
                        <option value="">Select Timezone</option>
                        @foreach ($timezones as $timezone)
                            <option value="{{ $timezone['id'] }}"
                                {{ old('timezone') == $timezone['id'] ? 'selected' : '' }}>
                                {{ $timezone['name'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('timezone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700" for="facebook_page_url">Page URL</label>
                    <input type="url" name="facebook_page_url" id="facebook_page_url" value="{{ old('facebook_page_url') }}"
                        class="form-input mt-1 block w-full rounded-md border-gray-300" required
                        placeholder="https://your-page"> 
                    @error('facebook_page_url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700" for="currency">Currency</label>
                    <select name="currency" id="currency"
                        class="form-select mt-1 block w-full rounded-md border-gray-300" required>
                        <option value="">Select Currency</option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency }}" {{ old('currency') == $currency ? 'selected' : '' }}>
                                {{ $currency }}
                            </option>
                        @endforeach
                    </select>
                    @error('currency')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700" for="business_manager_id">Business Manager ID</label>
                    <input type="text" name="business_manager_id" id="business_manager_id"
                        value="{{ old('business_manager_id') }}"
                        class="form-input mt-1 block w-full rounded-md border-gray-300" pattern="[A-Za-z0-9]{1,16}"
                        title="Must be between 1 and 16 characters long">
                    @error('business_manager_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-gray-700" for="landing_page">Landing Page</label>
                    <input type="url" name="landing_page" id="landing_page" value="{{ old('landing_page') }}"
                        class="form-input mt-1 block w-full rounded-md border-gray-300">
                    @error('landing_page')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 btn rounded-md">
                        Create Ad Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-user-layout>
