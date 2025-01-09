<x-admin-layout page="organizations">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex items-center justify-between pb-6">
                <div>
                    <h1 class="text-3xl text-black">Members of {{ $organization->name }}</h1>
                    <div class="mt-4">
                        <form action="" method="GET" class="flex items-center">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Search by name..." 
                                   value="{{ request('search') }}"
                                   class="rounded-l px-4 py-2 border focus:outline-none focus:border-blue-500">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-r">
                                Search
                            </button>
                        </form>
                    </div>
                </div>
                <a href="{{ route('admin.organizations.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Organizations
                </a>
            </div>
            
            <div class="w-full mt-6">
                <div class="bg-white shadow-md rounded my-6">
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Name</th>
                                <th class="py-3 px-6 text-left">Email</th>
                                <th class="py-3 px-6 text-center">Role</th>
                                <th class="py-3 px-6 text-center">Joined At</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($members as $member)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $member->name }}</td>
                                <td class="py-3 px-6 text-left">{{ $member->email }}</td>
                                <td class="py-3 px-6 text-center">
                                    <span class="bg-blue-200 text-blue-600 py-1 px-3 rounded-full text-xs">
                                        {{ ucfirst($member->pivot->role) }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">{{ $member->pivot->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</x-admin-layout>
