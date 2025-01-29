<x-user-layout page="organizations">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <div class="flex justify-between items-center pb-6">
                <h1 class="text-3xl text-black">My Businesses</h1>
                <a href="{{ route('organizations.create') }}" 
                    class="bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Create Business
                </a>
            </div>

            <div class="w-full mt-6">
                <div class="bg-white overflow-auto p-6">
                    @if ($organizations->isEmpty())
                        <p class="text-gray-600 text-center py-4">You are not a member of any businesses yet.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($organizations as $org)
                                <div class="card border rounded-lg shadow-sm p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-900">{{ $org->name }}</h3>
                                            <p class="text-sm text-gray-600">Role: {{ ucfirst($org->pivot->role) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex justify-end mt-4">
                                        @if (in_array($org->pivot->role, ['owner', 'admin']))
                                            <a href="{{ route('organization.invites', $org) }}"
                                                class="card-btn font-bold py-2 px-5  focus:outline-none focus:shadow-outline">
                                                Manage
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</x-user-layout>
