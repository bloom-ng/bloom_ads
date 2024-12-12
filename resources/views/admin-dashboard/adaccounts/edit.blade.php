<x-admin-layout page="adaccounts">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl text-black pb-6">Edit Ad Account</h1>
                <a href="{{ route('admin.adaccounts.index') }}" class="text-blue-500 hover:text-blue-700">
                    ← Back to Ad Accounts
                </a>
            </div>

            <div class="w-full mt-6">
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <form action="{{ route('admin.adaccounts.update', $adAccount->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                                Name
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                   id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name', $adAccount->name) }}"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                                Type
                            </label>
                            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="type"
                                    name="type"
                                    required>
                                <option value="meta" {{ $adAccount->type === 'meta' ? 'selected' : '' }}>Meta</option>
                                <option value="google" {{ $adAccount->type === 'google' ? 'selected' : '' }}>Google</option>
                                <option value="tiktok" {{ $adAccount->type === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                                Status
                            </label>
                            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="status"
                                    name="status"
                                    required>
                                <option value="processing" {{ $adAccount->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="pending" {{ $adAccount->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $adAccount->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="banned" {{ $adAccount->status === 'banned' ? 'selected' : '' }}>Banned</option>
                                <option value="deleted" {{ $adAccount->status === 'deleted' ? 'selected' : '' }}>Deleted</option>
                                <option value="rejected" {{ $adAccount->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="business_manager_id">
                                Business Manager ID
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                   id="business_manager_id"
                                   type="text"
                                   name="business_manager_id"
                                   value="{{ old('business_manager_id', $adAccount->business_manager_id) }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="landing_page">
                                Landing Page
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                   id="landing_page"
                                   type="url"
                                   name="landing_page"
                                   value="{{ old('landing_page', $adAccount->landing_page) }}">
                        </div>

                        <div class="flex items-center justify-end">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                    type="submit">
                                Update Ad Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-admin-layout> 