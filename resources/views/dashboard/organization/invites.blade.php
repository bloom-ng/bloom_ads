<x-user-layout page="organizations">
    <div class="dashboard text w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full flex-grow p-6">
            <h1 class="text-3xl pb-6">Business Invites</h1>

            <div class="dashboard text w-full mt-6">
                <div class="overflow-auto">
                    <!-- Invite Form -->
                    <div class="p-6 border-b border-gray-200">
                        <form method="POST" action="{{ route('organization.invite', $organization) }}" class="space-y-4">
                            @csrf
                            <div class="flex flex-wrap -mx-3 mb-6">
                                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                    <label class="block uppercase tracking-wide text-xs font-bold mb-2"
                                        for="email">
                                        Email Address
                                    </label>
                                    <input
                                        class="appearance-none block w-full bg-gray-200 text-black border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        id="email" type="email" name="email" required
                                        placeholder="user@example.com">
                                </div>
                                <div class="w-full md:w-1/2 px-3">
                                    <label class="block uppercase tracking-wide text-xs font-bold mb-2"
                                        for="role">
                                        Role
                                    </label>
                                    <select
                                        class="block appearance-none w-full bg-gray-200 text-black border border-gray-200 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                        id="role" name="role" required>
                                        <option value="admin">Admin</option>
                                        <option value="finance">Finance</option>
                                        <option value="advertiser">Advertiser</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex items-center justify-end">
                                <button type="submit" class="px-4 py-2 btn rounded-md font-semibold">
                                    Send Invitation
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Pending Invites Table -->
                    <div class="dashboard text p-6">
                        <h2 class="text-xl pb-3">Pending Invitations</h2>
                        <table class="min-w-full">
                            <thead class="table-header text-black">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Role</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Sent</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Expires</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-body text-gray-700">
                                @foreach ($pendingInvites as $invite)
                                    <tr>
                                        <td class="text-left py-3 px-4">{{ $invite->email }}</td>
                                        <td class="text-left py-3 px-4">{{ ucfirst($invite->role) }}</td>
                                        <td class="text-left py-3 px-4">{{ $invite->created_at->diffForHumans() }}</td>
                                        <td class="text-left py-3 px-4">{{ $invite->expires_at->diffForHumans() }}</td>
                                        <td class="text-left py-3 px-4">
                                            <form method="POST"
                                                action="{{ route('organization.invite.cancel', [$organization, $invite]) }}"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to cancel this invitation?')">
                                                    Cancel
                                                </button>
                                            </form>
                                            <form method="POST"
                                                action="{{ route('organization.invite.resend', [$organization, $invite]) }}"
                                                class="inline ml-4">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                    Resend
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @if ($pendingInvites->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-3 px-4">No pending invitations</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Business Members Table -->
                    <div class="dashboard text p-6">
                        <h2 class="text-xl pb-3">Business Members</h2>
                        <table class="min-w-full">
                            <thead class="table-header text-black">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Role</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Joined</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-body text-gray-700">
                                @foreach ($organization->users as $user)
                                    <tr>
                                        <td class="text-left py-3 px-4">{{ $user->name }}</td>
                                        <td class="text-left py-3 px-4">{{ $user->email }}</td>
                                        <td class="text-left py-3 px-4">{{ ucfirst($user->pivot->role) }}</td>
                                        <td class="text-left py-3 px-4">{{ $user->pivot->created_at->diffForHumans() }}
                                        </td>
                                        <td class="text-left py-3 px-4">
                                            @if ($user->id !== auth()->id())
                                                <form method="POST"
                                                    action="{{ route('organization.members.remove', [$organization, $user]) }}"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Are you sure you want to remove this member?')">
                                                        Remove
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">You</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-user-layout>
