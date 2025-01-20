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
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                            Linked
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300 text-right">
                                            <span class="inline-flex items-center justify-center w-6 h-6 {{ $account['is_linked'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }} rounded-full">
                                                {{ $account['is_linked'] ? '✓' : '✗' }}
                                            </span>
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
                                            <div class="flex justify-center space-x-3">
                                                <a href="https://business.facebook.com/adsmanager/manage/accounts?act={{ $account['account_id'] }}" 
                                                   target="_blank"
                                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                    View in Ads Manager
                                                </a>
                                                @if($account['is_linked'] === false)
                                                    <button 
                                                        onclick="openLinkAccountModal('{{ $account['id'] }}', '{{ $account['name'] }}', '{{ $account['currency'] }}')"
                                                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                        Link Account
                                                    </button>
                                                @else
                                                <button 
                                                        onclick="unlinkAccount('{{ $account['_adaccount']->id }}')"
                                                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                                        Unlink Account
                                                    </button>
                                                @endif
                                            </div>
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
                                        Link
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
    
    <x-modal name="link-ad-account" :show="false">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Link Ad Account
            </h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Organization
                </label>
                <select 
                    id="organization-select"
                    class="w-full mt-1 block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    onchange="handleOrganizationChange(this.value)">
                    <option value="">Select an organization...</option>
                </select>
            </div>
    
            <!-- Add Ad Account select -->
            <div class="mb-4" id="ad-account-select-container" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Select Ad Account
                </label>
                <select 
                    id="ad-account-select"
                    class="w-full mt-1 block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Select an ad account...</option>
                </select>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button 
                    type="button"
                    onclick="closeModal()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition">
                    Cancel
                </button>
                <button 
                    type="button"
                    onclick="handleLink()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    Link
                </button>
            </div>
        </div>
    </x-modal>
    
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
    <style>
        /* Base styles */
        .ts-wrapper.single .ts-control {
            @apply bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md;
            @apply text-sm text-gray-900 dark:text-gray-100;
            @apply min-h-[38px] px-3 py-2;
            @apply focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
        }

        /* Input placeholder */
        .ts-wrapper.single .ts-control input {
            @apply text-sm text-gray-900 dark:text-gray-100;
        }

        /* Dropdown styles */
        .ts-dropdown {
            @apply mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md;
            @apply shadow-lg;
        }

        /* Dropdown options */
        .ts-dropdown .option {
            @apply px-4 py-2 text-sm text-gray-900 dark:text-gray-100;
            @apply hover:bg-gray-100 dark:hover:bg-gray-600;
        }

        /* Active/selected option */
        .ts-dropdown .active {
            @apply bg-blue-50 dark:bg-blue-900 text-blue-900 dark:text-blue-100;
        }

        /* Selected item in input */
        .ts-wrapper.single .item {
            @apply text-sm text-gray-900 dark:text-gray-100;
        }

        /* Dropdown arrow */
        .ts-wrapper.single .ts-control::after {
            @apply border-gray-400 dark:border-gray-500;
        }

        /* Clear button */
        .ts-wrapper .clear-button {
            @apply text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300;
        }
    </style>
    @endpush
    
    
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
    let selectedAccountId = null;
    let selectedAccountName = null;
    let selectedOrganizationId = null;
    let selectedAdAccountId = null;
    let selectedAccountCurrency = null;

    function openLinkAccountModal(accountId, accountName, currency) {
        console.log('Opening modal for account ID:', accountId, 'and name:', accountName);
        selectedAccountId = accountId;
        selectedAccountName = accountName;
        selectedAccountCurrency = currency;
        fetchOrganizations();
        const event = new CustomEvent('open-modal', { detail: 'link-ad-account' });
        window.dispatchEvent(event);
    }

    function handleOrganizationChange(value) {
        selectedOrganizationId = value;
        if (value) {
            fetchAdAccounts(value);
            document.getElementById('ad-account-select-container').style.display = 'block';
        } else {
            document.getElementById('ad-account-select-container').style.display = 'none';
            const adAccountSelect = document.getElementById('ad-account-select');
            adAccountSelect.value = '';
            selectedAdAccountId = null;
        }
    }

    async function fetchAdAccounts(organizationId) {
        try {
            const response = await fetch(`/admin/data/organizations/${organizationId}/ad-accounts`, {
                headers: {
                    'Accept': 'application/json',
                }
            });
            
            if (!response.ok) throw new Error('Failed to fetch ad accounts');
            const adAccounts = await response.json();
            
            console.log('Ad Accounts:', adAccounts);

            // Get the select element
            const select = document.getElementById('ad-account-select');
            
            // Clear existing options except the first one
            while (select.options.length > 1) {
                select.remove(1);
            }

            // Add new options
            adAccounts.forEach(account => {
                const option = new Option(account.name, account.id);
                select.add(option);
            });
            
        } catch (error) {
            console.error('Error fetching ad accounts:', error);
            alert('Failed to load ad accounts');
        }
    }

    async function handleLink() {
        if (!selectedOrganizationId) {
            alert('Please select an organization');
            return;
        }

        if (!selectedAdAccountId) {
            alert('Please select an ad account');
            return;
        }

        try {
            const response = await fetch('/admin/meta/ad-accounts/link', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    account_id: selectedAccountId, //meta account id
                    ad_account_id: selectedAdAccountId,
                    ad_account_name: selectedAccountName,
                    currency: selectedAccountCurrency,
                    business_manager_id:  "{{ $businessManager->id }}"
                })
            });

            if (!response.ok) throw new Error('Failed to link account');

            // Show success message and close modal
            alert('Account linked successfully');
            closeModal();
            
            // Refresh the page to show updated data
            window.location.reload();
            
        } catch (error) {
            console.error('Error linking account:', error);
            alert('Failed to link account. Please try again.');
        }
    }

    // Add event listener for ad account select
    document.getElementById('ad-account-select').addEventListener('change', function(e) {
        selectedAdAccountId = e.target.value;
    });

    // Update close modal to reset ad account select
    function closeModal() {
        const event = new CustomEvent('close-modal', { detail: 'link-ad-account' });
        window.dispatchEvent(event);
        selectedAccountId = null;
        selectedOrganizationId = null;
        selectedAdAccountId = null;
        document.getElementById('organization-select').value = '';
        document.getElementById('ad-account-select').value = '';
        document.getElementById('ad-account-select-container').style.display = 'none';
    }

    // Update clean up event listener
    window.addEventListener('close-modal', (event) => {
        if (event.detail === 'link-ad-account') {
            selectedAccountId = null;
            selectedOrganizationId = null;
            selectedAdAccountId = null;
            document.getElementById('organization-select').value = '';
            document.getElementById('ad-account-select').value = '';
            document.getElementById('ad-account-select-container').style.display = 'none';
        }
    });

    async function fetchOrganizations() {
        try {
            const response = await fetch('/admin/data/organizations', {
                headers: {
                    'Accept': 'application/json',
                }
            });
            
            if (!response.ok) throw new Error('Failed to fetch organizations');
            const organizations = await response.json();
            
            console.log('Organizations:', organizations);

            // Get the select element
            const select = document.getElementById('organization-select');
            
            // Clear existing options except the first one
            while (select.options.length > 1) {
                select.remove(1);
            }

            // Add new options
            organizations.forEach(org => {
                const option = new Option(org.name, org.id);
                select.add(option);
            });
            
        } catch (error) {
            console.error('Error fetching organizations:', error);
            alert('Failed to load organizations');
        }
    }

    
    async function unlinkAccount(adAccountId) {
        if (!confirm('Are you sure you want to unlink this Ad account?')) {
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

