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
                        @foreach($userSettings as $setting)
                            @foreach($setting->preferences as $name => $value)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $name }}</td>
                                    <td class="py-3 px-6 text-left">
                                        <form method="POST" action="{{ route('admin.adminsettings.update', $setting) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="name" value="{{ $name }}">
                                            <input type="text" name="value" value="{{ $value }}" class="border rounded px-2 py-1 w-full">
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-admin-layout>
