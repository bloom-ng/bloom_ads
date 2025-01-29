<x-admin-layout page="business-managers">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center pb-6">
                <h1 class="text-3xl text-black dark:text-white">Business Managers</h1>
                <a href="{{ route('admin.business-managers.create') }}" 
                   class="btn hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Manager
                </a>
            </div>

            <div class="w-full mt-6">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded my-6">
                    @if($managers->isEmpty())
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            No business managers found.
                        </div>
                    @else
                        <table class="min-w-max w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Name</th>
                                    <th class="py-3 px-6 text-left">Platform</th>
                                    <th class="py-3 px-6 text-left">Portfolio ID</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 dark:text-gray-400 text-sm">
                                @foreach($managers as $manager)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <td class="py-3 px-6 text-left">{{ $manager->name }}</td>
                                    <td class="py-3 px-6 text-left">{{ $manager->platform }}</td>
                                    <td class="py-3 px-6 text-left">{{ $manager->portfolio_id }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex justify-center space-x-4">
                                            <a href="{{ route('admin.business-managers.accounts', $manager->id) }}" 
                                               class="text-blue-500 hover:text-blue-700">
                                                View Accounts
                                            </a>
                                            <a href="{{ route('admin.business-managers.edit', $manager->id) }}" 
                                               class="text-yellow-500 hover:text-yellow-700">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.business-managers.destroy', $manager->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this manager?');">
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
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-admin-layout> 