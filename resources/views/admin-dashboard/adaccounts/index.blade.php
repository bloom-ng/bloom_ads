<x-admin-layout page="adaccounts">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            {{-- Flash Messages --}}
            @if(session()->has('info'))
                <div class="bg-blue-100 dark:bg-blue-900 border-l-4 border-blue-500 text-blue-700 dark:text-blue-300 p-4 mb-4">
                    {{ session('info') }}
                </div>
            @endif
            @if(session()->has('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex justify-between items-center pb-6">
                <h1 class="text-3xl text-black dark:text-white">Ad Accounts</h1>
                <a href="{{ route('admin.adaccounts.export.filtered', request()->query()) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Export Listed Accounts
                </a>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.adaccounts.index') }}" class="mb-6">
                <div class="flex space-x-4">
                    <input type="text" name="name" placeholder="Filter by Name" value="{{ request('name') }}"
                           class="border rounded px-3 py-2">
                    <select name="status" class="border rounded px-3 py-2">
                        <option value="">Filter by Status</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Filter
                    </button>
                </div>
            </form>

            <div class="w-full mt-6">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded my-6">
                    @if($adAccounts->isEmpty())
                        <div class="p-6 text-center text-gray-500">
                            No ad accounts found for the specified filter criteria.
                        </div>
                    @else
                        <table class="min-w-max w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">ID</th>
                                    <th class="py-3 px-6 text-left">Name</th>
                                    <th class="py-3 px-6 text-left">Type</th>
                                    <th class="py-3 px-6 text-left">Organization</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                    <th class="py-3 px-6 text-center">Created By</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                                @foreach($adAccounts as $adAccount)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
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
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-admin-layout> 