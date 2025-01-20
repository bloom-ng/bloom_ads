<x-admin-layout page="organizations">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl text-black dark:text-white pb-6">Business: {{ $organization->name }}</h1>
                <a href="{{ route('admin.organizations.index') }}" class="text-blue-500 hover:text-blue-700">
                    ← Back to Businesses
                </a>
            </div>
            
            <div class="w-full mt-6">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded my-6">
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">ID</th>
                                <th class="py-3 px-6 text-left">Name</th>
                                <th class="py-3 px-6 text-left">Type</th>
                                <th class="py-3 px-6 text-left">Provider</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-center">Created By</th>
                                <th class="py-3 px-6 text-center">Created At</th>
                                <th class="py-3 px-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                            @foreach($adAccounts as $adAccount)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="py-3 px-6 text-left">{{ $adAccount->id }}</td>
                                <td class="py-3 px-6 text-left">{{ $adAccount->provider_account_name ? $adAccount->provider_account_name : $adAccount->name }}</td>
                                <td class="py-3 px-6 text-left">{{ $adAccount->type }}</td>
                                <td class="py-3 px-6 text-left">{{ $adAccount->provider }}</td>
                                <td class="py-3 px-6 text-center">
                                    <span class="bg-{{ $adAccount->status === 'approved' ? 'green' : ($adAccount->status === 'processing' ? 'yellow' : 'red') }}-200 text-{{ $adAccount->status === 'approved' ? 'green' : ($adAccount->status === 'processing' ? 'yellow' : 'red') }}-600 py-1 px-3 rounded-full text-xs">
                                        {{ ucfirst($adAccount->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">{{ $adAccount->user->name }}</td>
                                <td class="py-3 px-6 text-center">{{ $adAccount->created_at->format('Y-m-d') }}</td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center space-x-4">
                                        <a href="{{ route('admin.adaccounts.show', $adAccount->id) }}"
                                            class="text-blue-500 hover:text-blue-700">
                                            View
                                        </a>
                                        <a href="{{ route('admin.adaccounts.edit', $adAccount->id) }}" 
                                           class="text-blue-500 hover:text-blue-700">
                                            Edit
                                        </a>
                                        @if ($adAccount->provider_id)
                                                    <button onclick="unlinkAccount('{{ $adAccount->id }}')"
                                                        class="text-yellow-500 hover:text-yellow-700">
                                                        Unlink Meta
                                                    </button>
                                                @endif
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
                </div>
            </div>
        </main>
    </div>

    <script>
        async function unlinkAccount(adAccountId) {
            if (!confirm('Are you sure you want to unlink this Meta account?')) {
                return;
            }

            try {
                const response = await fetch('/admin/adaccounts/unlink-meta', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        ad_account_id: adAccountId
                    })
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Failed to unlink account');
                }
            } catch (error) {
                alert('Error unlinking account. Please try again.');
            }
        }
    </script>
</x-admin-layout> 