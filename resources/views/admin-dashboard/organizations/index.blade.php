<x-admin-layout page="organizations">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Organizations</h1>
            
            <div class="w-full mt-6">
                <div class="bg-white shadow-md rounded my-6">
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">ID</th>
                                <th class="py-3 px-6 text-left">Name</th>
                                <th class="py-3 px-6 text-left">Owner</th>
                                <th class="py-3 px-6 text-center">Members</th>
                                <th class="py-3 px-6 text-center">Created At</th>
                                <th class="py-3 px-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($organizations as $organization)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $organization->id }}</td>
                                <td class="py-3 px-6 text-left">{{ $organization->name }}</td>
                                <td class="py-3 px-6 text-left">{{ $organization->user->name }}</td>
                                <td class="py-3 px-6 text-center">{{ $organization->users->count() }}</td>
                                <td class="py-3 px-6 text-center">{{ $organization->created_at->format('Y-m-d') }}</td>
                                <td class="py-3 px-6 text-center">
                                    <a href="{{ route('admin.organizations.show', $organization->id) }}" class="text-blue-500 hover:text-blue-700">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</x-admin-layout> 