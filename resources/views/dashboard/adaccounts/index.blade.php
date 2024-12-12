<x-user-layout page="adaccounts">
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center">
            <h3 class="text-gray-700 text-3xl font-medium">Ad Accounts</h3>
            <a href="{{ route('adaccounts.create') }}" 
               class="px-4 py-2 bg-[#F48857] text-gray-700 rounded-md hover:bg-[#F48857]/80">
                Create Ad Account
            </a>
        </div>

        <div class="mt-8">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Name</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Platform</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Currency</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Organization</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Actions</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adAccounts as $account)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $account->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $account->type }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $account->currency }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $account->organization->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                <div class="flex space-x-4">
                                    <a href="{{ route('adaccounts.edit', $account->id) }}" 
                                       class="text-[#F48857] hover:text-[#F48857]/80">
                                        Edit
                                    </a>
                                    @if($account->status === 'processing')
                                        <form action="{{ route('adaccounts.destroy', $account->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this ad account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 uppercase py-4 whitespace-no-wrap border-b border-gray-500">{{ $account->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-user-layout> 