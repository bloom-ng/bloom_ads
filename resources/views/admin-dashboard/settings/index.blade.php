<x-admin-layout page="settings">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center pb-6">
                <h1 class="text-3xl text-black">Admin Settings</h1>
                <a href="{{ route('admin.adminsettings.create') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create New Setting
                </a>
            </div>

            <!-- Search and Sort -->
            <div class="mb-6">
                <form method="GET" class="flex gap-4">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search settings..."
                           class="border rounded px-4 py-2 w-full max-w-md">
                    
                    <select name="sort" class="border rounded px-4 py-2">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                            Newest First
                        </option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                            Oldest First
                        </option>
                    </select>

                    <button type="submit" 
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Settings Table -->
            <div class="bg-white shadow-md rounded my-6">
                <table class="min-w-max w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Name</th>
                            <th class="py-3 px-6 text-left">Key</th>
                            <th class="py-3 px-6 text-left">Value</th>
                            <th class="py-3 px-6 text-center">Last Updated</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @forelse($adminSettings as $setting)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $setting->name }}</td>
                                <td class="py-3 px-6 text-left">{{ $setting->key }}</td>
                                <td class="py-3 px-6 text-left">{{ $setting->value }}</td>
                                <td class="py-3 px-6 text-center">{{ $setting->updated_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3 px-6 text-center">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button @click="open = !open" class="flex items-center text-gray-600 hover:text-gray-800">
                                            <span class="mr-2">Actions</span>
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        
                                        <div x-show="open" 
                                             @click.away="open = false"
                                             class="absolute right-0 w-48 py-2 mt-2 bg-white rounded-md shadow-xl z-20">
                                            <a href="{{ route('admin.adminsettings.edit', $setting) }}" 
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.adminsettings.destroy', $setting) }}" 
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this setting?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">
                                    No settings found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-admin-layout>
