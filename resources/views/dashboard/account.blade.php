<x-user-layout :page="$page">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Account Settings</h1>

            <div class="w-full max-w-md">
                <form method="POST" action="{{ route('account.update') }}" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Name
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name', auth()->user()->name) }}"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100" 
                            id="email" 
                            type="email" 
                            value="{{ auth()->user()->email }}"
                            disabled>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                            Phone Number
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="phone" 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', auth()->user()->phone) }}">
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