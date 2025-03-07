<x-user-layout page="adaccounts">
    <div class="container dashboard -mx-auto px-6 py-8">
        <!-- Flash Messages -->
        @if (session('info'))
            <div class="bg-blue-100 dark:bg-blue-900 border-l-4 border-blue-500 text-blue-700 dark:text-blue-300 p-4 mb-4"
                role="alert">
                <p class="font-medium">{{ session('info') }}</p>
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 mb-4"
                role="alert">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-4"
                role="alert">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="flex justify-between items-center">
            <h3 class="text-black text-3xl text font-medium">Ad Accounts</h3>
            <a href="{{ route('adaccounts.create') }}" class="px-4 py-2 btn rounded-md">
                Create Ad Account
            </a>
        </div>

        <div class="mt-8">
            <div class="overflow-x-auto">
                <table class="min-w-full shadow-md rounded my-6">
                    <thead class="table-header">
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Name</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Platform</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Currency</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Organization</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        @foreach ($adAccounts as $account)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ $account->provider_account_name ? $account->provider_account_name : $account->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $account->type }}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ $account->currency }}</td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ $account->organization->name }}</td>
                                <td class="px-6 uppercase py-4 whitespace-no-wrap border-b border-gray-500">
                                    {{ $account->status }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium space-x-2">
                                    <a href="{{ route('adaccounts.show', $account->id) }}"
                                        class="text-[#000080] text-lg font-bold">
                                        Manage
                                    </a>

                                    @if ($account->status === 'processing')
                                        <a href="{{ route('adaccounts.edit', $account->id) }}" class="text-[#000080]">
                                            Edit
                                        </a>

                                        <form action="{{ route('adaccounts.destroy', $account->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this ad account?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-user-layout>
