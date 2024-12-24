<x-admin-layout page="meta">
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
                <h1 class="text-3xl text-black">Meta Ad Accounts</h1>
            </div>

            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="mr-2">
                        <a href="{{ request()->fullUrlWithQuery(['tab' => 'owned', 'before' => null, 'after' => null]) }}" 
                           class="inline-block p-4 {{ $activeTab === 'owned' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-600 hover:border-gray-300 border-b-2 border-transparent' }}">
                            Owned Accounts
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="{{ request()->fullUrlWithQuery(['tab' => 'client', 'before' => null, 'after' => null]) }}"
                           class="inline-block p-4 {{ $activeTab === 'client' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-600 hover:border-gray-300 border-b-2 border-transparent' }}">
                            Client Accounts
                        </a>
                    </li>
                </ul>
            </div>

            <div class="w-full mt-6">
                <div class="bg-white shadow-md rounded my-6">
                    @if(empty($adAccounts) || $adAccounts->isEmpty())
                        <div class="p-6 text-center text-gray-500">
                            No Meta ad accounts found.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full">
                                <div class="overflow-hidden">
                                    <table class="min-w-full">
                                        <thead class="bg-gray-200">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                    Account ID
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                    Name
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                    Currency
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                    Amount Spent
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                    Balance
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($adAccounts as $account)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $account['id'] ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $account['name'] ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                                    {{ $account['currency'] ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                                    {{ number_format($account['amount_spent']) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                                    {{ number_format($account['balance']) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $account['status'] === 'ACTIVE' 
                                                            ? 'bg-green-100 text-green-800' 
                                                            : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst(strtolower($account['status'] ?? 'Unknown')) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                    <a href="https://business.facebook.com/adsmanager/manage/accounts?act={{ $account['account_id'] }}" 
                                                       target="_blank"
                                                       class="text-blue-600 hover:text-blue-900">
                                                        View in Ads Manager
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 flex justify-between items-center">
                            <div class="flex space-x-4">
                                @if($prevCursor && $hasPrev)
                                    <a href="{{ request()->fullUrlWithQuery(['before' => $prevCursor, 'after' => null, 'tab' => $activeTab]) }}" 
                                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        Previous
                                    </a>
                                @endif

                                @if($nextCursor && $hasNext)
                                    <a href="{{ request()->fullUrlWithQuery(['after' => $nextCursor, 'before' => null, 'tab' => $activeTab]) }}" 
                                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center">
                                        Next
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-admin-layout> 