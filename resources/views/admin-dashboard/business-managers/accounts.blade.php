<x-admin-layout page="business-managers">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center pb-6">
                <h1 class="text-3xl text-black dark:text-white">
                    Ad Accounts for {{ $businessManager->name }}
                </h1>
                <a href="{{ route('admin.business-managers.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Managers
                </a>
            </div>

            <!-- Tabs -->
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="mr-2">
                        <a href="{{ request()->fullUrlWithQuery(['tab' => 'client', 'before' => null, 'after' => null]) }}" 
                           class="inline-block p-4 {{ $activeTab === 'client' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'text-gray-500 hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 border-b-2 border-transparent' }}">
                            Client Accounts
                        </a>
                    </li>
                    <li class="mr-2">
                        <a href="{{ request()->fullUrlWithQuery(['tab' => 'owned', 'before' => null, 'after' => null]) }}"
                           class="inline-block p-4 {{ $activeTab === 'owned' ? 'text-blue-600 border-b-2 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'text-gray-500 hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 border-b-2 border-transparent' }}">
                            Owned Accounts
                        </a>
                    </li>
                </ul>
            </div>

            <div class="w-full mt-6">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded my-6">
                    @if($adAccounts->isEmpty())
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            No {{ $activeTab }} ad accounts found.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-200 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            Account ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            Currency
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            Amount Spent
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            Balance
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($adAccounts as $account)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $account['id'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ $account['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300 text-center">
                                            {{ $account['currency'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300 text-right">
                                            {{ number_format($account['amount_spent']) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300 text-right">
                                            {{ number_format($account['balance']) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $account['status'] === 'ACTIVE' 
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' 
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                                {{ $account['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="https://business.facebook.com/adsmanager/manage/accounts?act={{ $account['account_id'] }}" 
                                               target="_blank"
                                               class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                View in Ads Manager
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 flex justify-between items-center border-t border-gray-200 dark:border-gray-700">
                            <div class="flex space-x-4">
                                @if($hasPrev)
                                    <a href="{{ request()->fullUrlWithQuery(['before' => $prevCursor, 'after' => null, 'tab' => $activeTab]) }}" 
                                       class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        Previous
                                    </a>
                                @endif

                                @if($hasNext)
                                    <a href="{{ request()->fullUrlWithQuery(['after' => $nextCursor, 'before' => null, 'tab' => $activeTab]) }}" 
                                       class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center">
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