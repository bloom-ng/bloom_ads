<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, Organization $organization)
    {
        $role = $organization->users()->where('user_id', $user->id)->first()->pivot->role;
        return in_array($role, ['owner', 'admin']);
    }
}
