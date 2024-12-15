<x-admin-layout page="users">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Users</h1>
            
            <div class="w-full">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Name</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Email</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Created At</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                <a class="text-green-500" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                                <a class="pl-2 text-yellow-500" href="">Orgs</a><br>
                                <a class="pl-4 text-red-500" href="">Delete</a>
                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 px-4">
                    {{ $users->links() }}
                </div>
            </div>
        </main>
    </div>
</x-admin-layout>
