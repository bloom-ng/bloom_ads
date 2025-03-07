<x-user-layout page="organizations">
    <div class="dashboard text w-full overflow-x-hidden border-t flex flex-col">
        <main class="dashboard text w-full flex-grow p-6">
            <div class="flex justify-between items-center pb-6">
                <h1 class="text-3xl">Create Business</h1>
                <a href="{{ route('organizations.index') }}"
                    class="px-4 py-2 btn-primary rounded-mdfont-bold rounded focus:outline-none focus:shadow-outline">
                    Back
                </a>
            </div>

            <div class="dashboard text w-full mt-6">
                <div class="overflow-auto p-6 rounded-lg shadow-sm">
                    <form method="POST" action="{{ route('organizations.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium">
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
                            <button type="submit" class="px-4 py-2 btn rounded-md">
                                Create Business
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-user-layout>
