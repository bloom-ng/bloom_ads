<x-admin-layout page="business-managers">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center pb-6">
                <h1 class="text-3xl text-black dark:text-white">
                    {{ isset($businessManager) ? 'Edit Business Manager' : 'Create Business Manager' }}
                </h1>
                <a href="{{ route('admin.business-managers.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>

            <div class="w-full mt-6">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded my-6 p-6">
                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($businessManager) 
                        ? route('admin.business-managers.update', $businessManager->id)
                        : route('admin.business-managers.store') }}" 
                          method="POST">
                        @csrf
                        @if(isset($businessManager))
                            @method('PUT')
                        @endif

                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="name">
                                Name
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name', $businessManager->name ?? '') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="platform">
                                Platform
                            </label>
                            <select name="platform" 
                                    id="platform"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('platform') border-red-500 @enderror"
                                    required>
                                <option value="">Select Platform</option>
                                <option value="meta" {{ (old('platform', $businessManager->platform ?? '') == 'meta') ? 'selected' : '' }}>Meta</option>
                                <option value="tiktok" {{ (old('platform', $businessManager->platform ?? '') == 'tiktok') ? 'selected' : '' }}>TikTok</option>
                            </select>
                            @error('platform')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="portfolio_id">
                                Portfolio ID (Business ID)
                            </label>
                            <input type="text" 
                                   name="portfolio_id" 
                                   id="portfolio_id"
                                   value="{{ old('portfolio_id', $businessManager->portfolio_id ?? '') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('portfolio_id') border-red-500 @enderror"
                                   required>
                            @error('portfolio_id')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="token">
                                Access Token
                            </label>
                            <textarea name="token" 
                                      id="token"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('token') border-red-500 @enderror"
                                      rows="3"
                                      required>{{ old('token', $businessManager->token ?? '') }}</textarea>
                            @error('token')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.business-managers.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ isset($businessManager) ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-admin-layout> 