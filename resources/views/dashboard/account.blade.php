<x-user-layout :page="$page">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text pb-6">Account Settings</h1>

            <div class="w-full max-w-md">
                <form method="POST" action="{{ route('account.update') }}" class="dashboard text shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="name">
                            Name
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-700 dark:text-white dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline" 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name', auth()->user()->name) }}"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="email">
                            Email
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-gray-600 dark:border-gray-600 bg-gray-100" 
                            id="email" 
                            type="email" 
                            value="{{ auth()->user()->email }}"
                            disabled>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="phone">
                            Phone Number
                            @if (auth()->user()->hasVerifiedPhone())
                                <span class="ml-2 text-xs font-semibold text-green-700 bg-green-100 rounded px-2 py-0.5">Verified</span>
                            @else
                                <span class="ml-2 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded px-2 py-0.5">Unverified</span>
                            @endif
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-gray-600 dark:border-gray-600 bg-gray-100"
                            id="phone"
                            type="text"
                            value="{{ auth()->user()->phone_country_code }} {{ auth()->user()->phone }}"
                            disabled>
                        <a href="{{ route('phone.notice') }}" class="inline-block mt-2 text-sm text-blue-500 hover:text-blue-700">
                            {{ auth()->user()->hasVerifiedPhone() ? 'Change number' : 'Verify number' }}
                        </a>
                    </div>

                    <div class="flex items-center justify-between">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                            type="submit">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-user-layout> 