<x-admin-layout page="adaccounts">



    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            {{-- Flash Messages --}}
            @if (session()->has('info'))
                <div
                    class="bg-blue-100 dark:bg-blue-900 border-l-4 border-blue-500 text-blue-700 dark:text-blue-300 p-4 mb-4">
                    {{ session('info') }}
                </div>
            @endif
            @if (session()->has('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
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
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                        </option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                        </option>
                    </select>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Filter
                    </button>
                </div>
            </form>

            <div class="w-full mt-6">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded my-6">
                    @if ($adAccounts->isEmpty())
                        <div class="p-6 text-center text-gray-500">
                            No ad accounts found for the specified filter criteria.
                        </div>
                    @else
                        <table class="min-w-max w-full table-auto">
                            <thead>
                                <tr
                                    class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
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
                                @foreach ($adAccounts as $adAccount)
                                    <tr
                                        class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="py-3 px-6 text-left">{{ $adAccount->id }}</td>
                                        <td class="py-3 px-6 text-left">{{ $adAccount->name }}</td>
                                        <td class="py-3 px-6 text-left">{{ $adAccount->type }}</td>
                                        <td class="py-3 px-6 text-left">{{ $adAccount->organization->name }}</td>
                                        <td class="py-3 px-6 text-center">
                                            <span
                                                class="bg-{{ $adAccount->status === 'approved' ? 'green' : ($adAccount->status === 'processing' ? 'yellow' : 'red') }}-200 text-{{ $adAccount->status === 'approved' ? 'green' : ($adAccount->status === 'processing' ? 'yellow' : 'red') }}-600 py-1 px-3 rounded-full text-xs">
                                                {{ ucfirst($adAccount->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-center">{{ $adAccount->user->name }}</td>
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
                                                @if (!$adAccount->provider_id)
                                                    <button onclick="showLinkModal('{{ $adAccount->id }}')"
                                                        class="text-green-500 hover:text-green-700">
                                                        Link Meta
                                                    </button>
                                                @else
                                                    <button onclick="unlinkAccount('{{ $adAccount->id }}')"
                                                        class="text-yellow-500 hover:text-yellow-700">
                                                        Unlink Meta
                                                    </button>
                                                @endif
                                                <form action="{{ route('admin.adaccounts.destroy', $adAccount->id) }}"
                                                    method="POST" class="inline"
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
    <!-- Meta Account Linking Modal -->
    <div id="linkMetaModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <input type="hidden" id="selectedAdAccountId">
        <div class="relative top-20 mx-auto p-5 border w-[90%] max-w-6xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Link Meta Ad Account</h3>
                <button onclick="hideModal()" class="text-gray-600 hover:text-gray-800">&times;</button>
            </div>

            <!-- Tabs -->
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg tab-button active" id="owned-tab"
                            data-tab="owned" role="tab">
                            Owned Accounts
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg tab-button"
                            id="client-tab" data-tab="client" role="tab">
                            Client Accounts
                        </button>
                    </li>
                </ul>
            </div>

            <div id="loader" class="hidden">
                <div class="flex justify-center items-center p-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>
            </div>

            <div id="metaAccountsList" class="max-h-[600px] overflow-y-auto">
                <div class="text-center p-4">Loading...</div>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-between items-center border-t pt-4">
                <button id="prevButton" onclick="changePage('prev')"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-gray-100 hidden">
                    Previous
                </button>
                <button id="nextButton" onclick="changePage('next')"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-gray-100 hidden">
                    Next
                </button>
            </div>
        </div>
    </div>


    <script>
        // Global variables for pagination
        let currentTab = 'owned';
        let currentCursor = null;
        let hasNextPage = false;
        let hasPrevPage = false;
        let cursors = {
            owned: {
                next: null,
                prev: null
            },
            client: {
                next: null,
                prev: null
            }
        };

        // Define functions in global scope
        function showLinkModal(adAccountId) {
            const modal = document.getElementById('linkMetaModal');
            document.getElementById('selectedAdAccountId').value = adAccountId;
            modal.classList.remove('hidden');
            currentTab = 'owned';
            currentCursor = null;
            updateActiveTab('owned');
            fetchMetaAccounts();
        }

        function hideModal() {
            const modal = document.getElementById('linkMetaModal');
            const accountsList = document.getElementById('metaAccountsList');
            modal.classList.add('hidden');
            accountsList.innerHTML = '<div class="text-center p-4">Loading...</div>';
            cursors = {
                owned: {
                    next: null,
                    prev: null
                },
                client: {
                    next: null,
                    prev: null
                }
            };
        }

        function updateActiveTab(tab) {
            document.querySelectorAll('.tab-button').forEach(button => {
                if (button.dataset.tab === tab) {
                    button.classList.add('active', 'border-blue-600', 'text-blue-600');
                    button.classList.remove('border-transparent', 'text-gray-500');
                } else {
                    button.classList.remove('active', 'border-blue-600', 'text-blue-600');
                    button.classList.add('border-transparent', 'text-gray-500');
                }
            });
        }

        async function changePage(direction) {
            if (direction === 'next') {
                currentCursor = cursors[currentTab].next;
            } else {
                currentCursor = cursors[currentTab].prev;
            }
            await fetchMetaAccounts();
        }

        // Update the fetchMetaAccounts function
        async function fetchMetaAccounts() {
            const accountsList = document.getElementById('metaAccountsList');
            const prevButton = document.getElementById('prevButton');
            const nextButton = document.getElementById('nextButton');
            const loader = document.getElementById('loader');

            try {
                // Show loader
                accountsList.classList.add('hidden');
                loader.classList.remove('hidden');

                const params = new URLSearchParams({
                    tab: currentTab,
                    limit: 30
                });

                if (currentCursor) {
                    if (cursors[currentTab].next === currentCursor) {
                        params.append('after', currentCursor);
                    } else {
                        params.append('before', currentCursor);
                    }
                }

                const response = await fetch(`/admin/meta-accounts/list?${params.toString()}`);
                const data = await response.json();

                // Update cursors
                cursors[currentTab].next = data.nextCursor;
                cursors[currentTab].prev = data.previousCursor;

                // Update pagination buttons visibility
                prevButton.classList.toggle('hidden', !data.hasPrev);
                nextButton.classList.toggle('hidden', !data.hasNext);

                // Disable/enable buttons based on data availability
                prevButton.disabled = !data.hasPrev;
                nextButton.disabled = !data.hasNext;

                let html = '<div class="space-y-4">';

                if (data[currentTab] && data[currentTab].length > 0) {
                    data[currentTab].forEach(account => {
                        html += `
                            <div class="p-3 border rounded mb-2 flex justify-between items-center">
                                <div>
                                    <div class="font-semibold">${account.name}</div>
                                    <div class="text-sm text-gray-600">ID: ${account.id}</div>
                                    <div class="text-xs text-gray-500">
                                        Status: ${account.status} | Currency: ${account.currency}
                                    </div>
                                </div>
                                <button onclick="linkAccount('${account.id}')" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">
                                    Link
                                </button>
                            </div>
                        `;
                    });
                } else {
                    html += '<div class="text-center text-gray-500 p-4">No accounts found</div>';
                }

                html += '</div>';

                // Hide loader and show content
                loader.classList.add('hidden');
                accountsList.classList.remove('hidden');
                accountsList.innerHTML = html;

                // Scroll to top of accounts list if this was a pagination change
                if (currentCursor) {
                    accountsList.scrollTop = 0;
                }
            } catch (error) {
                // Hide loader and show error
                loader.classList.add('hidden');
                accountsList.classList.remove('hidden');
                accountsList.innerHTML = '<div class="text-red-500 p-4">Error loading accounts</div>';
            }
        }

        // Add event listeners when the document is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Add click handlers for tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', async () => {
                    // Update active tab immediately for better UX
                    updateActiveTab(button.dataset.tab);
                    currentTab = button.dataset.tab;
                    currentCursor = null;
                    await fetchMetaAccounts();
                });
            });
        });

        async function linkAccount(metaAccountId) {
            const adAccountId = document.getElementById('selectedAdAccountId').value;

            try {
                const response = await fetch('/admin/adaccounts/link-meta', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        ad_account_id: adAccountId,
                        meta_account_id: metaAccountId
                    })
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Failed to link account');
                }
            } catch (error) {
                alert('Error linking account. Please try again.');
            }
        }

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
