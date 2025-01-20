<x-user-layout page="organizations">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center pb-6">
                <h1 class="text-3xl text-black">Create Business</h1>
                <a href="{{ route('organizations.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Back
                </a>
            </div>

            <div class="w-full mt-6">
                <div class="bg-white overflow-auto p-6 rounded-lg shadow-sm">
                    <form method="POST" action="{{ route('organizations.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Business Name
                            </label>
                            <input type="text" name="name" id="name" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F48857] focus:ring-[#F48857] sm:text-sm px-2 py-3"
                                placeholder="Enter business name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Create Business
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-user-layout>
