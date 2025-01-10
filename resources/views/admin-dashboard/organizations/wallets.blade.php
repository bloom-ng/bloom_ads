<x-admin-layout page="organizations">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6 bg-body">
            <div class="flex items-center justify-between pb-6">
                <div>
                    <h1 class="text-3xl text-black text pb-6">Wallets of {{ $organization->name }}</h1>
                </div>
                <a href="{{ route('admin.organizations.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Organizations
                </a>
            </div>
            
            <div class="w-full mt-6">
                <div class="bg-white shadow-md rounded my-6">
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">ID</th>
                                <th class="py-3 px-6 text-left">Balance</th>
                                <th class="py-3 px-6 text-left">Currency</th>
                                <th class="py-3 px-6 text-center">Created At</th>
                                <th class="py-3 px-6 text-center">Updated At</th>
                                <th class="py-3 px-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach($wallets as $wallet)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $wallet->id }}</td>
                                <td class="py-3 px-6 text-left">{{ number_format($wallet->calculated_balance, 2) }}</td>
                                <td class="py-3 px-6 text-left">{{ $wallet->currency }}</td>
                                <td class="py-3 px-6 text-center">{{ $wallet->created_at->format('Y-m-d') }}</td>
                                <td class="py-3 px-6 text-center">{{ $wallet->updated_at->format('Y-m-d') }}</td>
                                <td class="py-3 px-6 text-center">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button @click="open = !open" class="flex items-center text-gray-600 hover:text-gray-800">
                                            <span class="mr-2">View</span>
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        
                                        <div x-show="open" 
                                             @click.away="open = false"
                                             class="absolute right-0 w-48 py-2 mt-2 bg-white rounded-md shadow-xl z-20"
                                             style="transform: translateY(-50%); margin-top: 2rem;">
                                            <a href="{{ route('admin.wallets.transactions', $wallet->id) }}"
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                View Transactions
                                            </a>
                                        </div>
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
</x-admin-layout>
