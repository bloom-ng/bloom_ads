<x-admin-layout page="rockads">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            {{-- Flash Messages --}}
            @if(session()->has('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
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
                <h1 class="text-3xl text-black">RockAds Accounts</h1>
            </div>

            <div class="w-full mt-6">
                <div class="bg-white shadow-md rounded my-6">
                    @if(empty($adAccounts) || $adAccounts->isEmpty())
                        <div class="p-6 text-center text-gray-500">
                            No RockAds accounts found.
                        </div>
                    @else
                        <table class="min-w-max w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Account ID</th>
                                    <th class="py-3 px-6 text-left">Name</th>
                                    <th class="py-3 px-6 text-left">Platform</th>
                                    <th class="py-3 px-6 text-center">Balance</th>
                                    <th class="py-3 px-6 text-center">Currency</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @foreach($adAccounts as $account)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $account->account_id }}</td>
                                    <td class="py-3 px-6 text-left">{{ $account->account_name }}</td>
                                    <td class="py-3 px-6 text-left">{{ $account->platform_id }}</td>
                                    <td class="py-3 px-6 text-center">{{ $account->balance }}</td>
                                    <td class="py-3 px-6 text-center">{{ $account->currency_code }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="bg-{{ $account->status === 'active' ? 'green' : 'red' }}-200 text-{{ $account->status === 'active' ? 'green' : 'red' }}-600 py-1 px-3 rounded-full text-xs">
                                            {{ ucfirst($account->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        @if($account->ads_manager_url)
                                            <a href="{{ $account->ads_manager_url }}" 
                                               target="_blank"
                                               class="text-blue-500 hover:text-blue-700">
                                                View in Ads Manager
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        <div class="px-6 py-4">
                            {{ $adAccounts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-admin-layout> 