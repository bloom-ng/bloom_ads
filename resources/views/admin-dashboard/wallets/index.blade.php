<x-admin-layout page="wallet">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">All Wallets</h1>
            
            <div class="w-full">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">User</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Organization</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Currency</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Balance</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wallets as $wallet)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                {{ $wallet->organization->users->first()->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                {{ $wallet->organization->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                {{ $wallet->currency }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                {{ number_format($wallet->balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                                {{ $wallet->created_at->format('Y-m-d') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-admin-layout> 