<x-admin-layout page="settings">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Admin Settings</h1>

            <div class="bg-white shadow-md rounded my-6 p-6">
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
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $setting->name }}</td>
                                <td class="py-3 px-6 text-left">
                                    <form method="POST" action="{{ route('admin.adminsettings.update', $setting->id) }}" class="flex items-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" 
                                               name="value" 
                                               value="{{ $setting->value }}" 
                                               class="border rounded px-2 py-1 w-full">
                                        <button type="submit" 
                                                class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <form method="POST" action="{{ route('admin.adminsettings.destroy', $setting->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded"
                                                onclick="return confirm('Are you sure you want to delete this setting?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

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
            </div>
        </main>
    </div>
</x-admin-layout>
