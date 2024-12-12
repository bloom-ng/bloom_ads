<x-admin-layout page="adaccounts">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
        <!-- Flash Messages -->
        @if(session('warning'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p>{{ session('warning') }}</p>
                </div>
            @endif
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="flex justify-between items-center pb-6">
                <h1 class="text-3xl text-black">Ad Accounts</h1>
                <a href="{{ route('admin.adaccounts.export.processing') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Export Processing Accounts
                </a>
            </div>
            
            <div class="w-full mt-6">
                <div class="bg-white shadow-md rounded my-6">
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">ID</th>
                                <th class="py-3 px-6 text-left">Name</th>
                                <th class="py-3 px-6 text-left">Type</th>
                                <th class="py-3 px-6 text-left">Organization</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-center">Created By</th>
                                <th class="py-3 px-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($adAccounts as $adAccount)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $adAccount->id }}</td>
                                <td class="py-3 px-6 text-left">{{ $adAccount->name }}</td>
                                <td class="py-3 px-6 text-left">{{ $adAccount->type }}</td>
                                <td class="py-3 px-6 text-left">{{ $adAccount->organization->name }}</td>
                                <td class="py-3 px-6 text-center">
                                    <span class="bg-{{ $adAccount->status === 'approved' ? 'green' : ($adAccount->status === 'processing' ? 'yellow' : 'red') }}-200 text-{{ $adAccount->status === 'approved' ? 'green' : ($adAccount->status === 'processing' ? 'yellow' : 'red') }}-600 py-1 px-3 rounded-full text-xs">
                                        {{ ucfirst($adAccount->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">{{ $adAccount->user->name }}</td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center space-x-4">
                                        <a href="{{ route('admin.adaccounts.edit', $adAccount->id) }}" 
                                           class="text-blue-500 hover:text-blue-700">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.adaccounts.destroy', $adAccount->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this ad account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4 px-4">
                    {{ $adAccounts->links() }}
                </div>
                </div>
                
            </div>
        </main>
    </div>
</x-admin-layout> 