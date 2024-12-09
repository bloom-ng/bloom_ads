<x-user-layout page="settings">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl text-black pb-6">Settings</h1>

            <div class="w-full mt-6">
                <div class="bg-white overflow-auto p-6 rounded-lg shadow-sm">
                    <!-- Current Organization Selection -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4">Current Organization</h2>
                        <div class="space-y-4">
                            @foreach ($organizations as $org)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <h3 class="font-medium">{{ $org->name }}</h3>
                                        <p class="text-sm text-gray-500">
                                            Role:
                                            {{ ucfirst($org->users->where('id', auth()->id())->first()?->pivot->role) }}
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('settings.set-organization') }}"
                                        class="flex items-center">
                                        @csrf
                                        <input type="hidden" name="organization_id" value="{{ $org->id }}">
                                        <button type="submit"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#F48857] focus:ring-offset-2 {{ $org->id === auth()->user()->settings?->current_organization_id ? 'bg-green-200' : 'bg-gray-200' }}"
                                            role="switch"
                                            aria-checked="{{ $org->id === auth()->user()->settings?->current_organization_id ? 'true' : 'false' }}">
                                            <span
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $org->id === auth()->user()->settings?->current_organization_id ? 'translate-x-5' : 'translate-x-0' }}">
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            @endforeach

                            @if ($organizations->isEmpty())
                                <div class="text-center py-8">
                                    <p class="text-gray-500 mb-4">You don't belong to any organizations yet.</p>
                                    <a href="{{ route('organizations.create') }}"
                                        class="bg-[#F48857] hover:bg-[#F48857]/90 text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        Create Organization
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-user-layout>
