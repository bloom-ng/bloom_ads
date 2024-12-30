<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        @if (auth()->user()->unreadNotifications->count() > 0)
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.away="open = false"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50">
        <div class="px-4 py-2 border-b">
            <h3 class="text-lg font-semibold">Notifications</h3>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse(auth()->user()->notifications as $notification)
                <div class="px-4 py-3 hover:bg-gray-50 {{ $notification->read_at ? 'opacity-50' : '' }}">
                    <p class="text-sm">
                        {{ $notification->data['message'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
            @empty
                <div class="px-4 py-3 text-gray-500 text-center">
                    No notifications
                </div>
            @endforelse
        </div>

        @if (auth()->user()->unreadNotifications->count() > 0)
            <div class="px-4 py-2 border-t">
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                        Mark all as read
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
