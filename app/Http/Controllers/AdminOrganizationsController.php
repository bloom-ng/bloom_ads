<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class AdminOrganizationsController extends Controller
{
    public function index()
    {
        $search = request('search');
        $organizations = Organization::with(['user', 'users'])
            ->when($search, function($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->get();
        return view('admin-dashboard.organizations.index', compact('organizations'));
    }

    public function show(Organization $organization)
    {
        $search = request('search');
        $adAccounts = $organization->adAccounts()
            ->with(['user'])
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('provider_account_name', 'like', '%' . $search . '%');
                });
            })
            ->get();
        return view('admin-dashboard.organizations.show', compact('organization', 'adAccounts'));
    }

    public function destroy(Organization $organization)
    {
        // Delete associated ad accounts first
        $organization->adAccounts()->delete();
        
        // Then delete the organization
        $organization->delete();
        
        return redirect()
            ->route('admin.organizations.index')
            ->with('success', 'Organization and its ad accounts deleted successfully');
    }

    public function members(Organization $organization)
    {
        $search = request('search');
        $members = $organization->users()
            ->withPivot('role')
            ->when($search, function($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->get();
            
        return view('admin-dashboard.organizations.members', compact('organization', 'members'));
    }

    public function wallets(Organization $organization)
    {
        $search = request('search');
        $wallets = $organization->wallets()
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('id', 'like', '%' . $search . '%')
                      ->orWhere('currency', 'like', '%' . $search . '%');
                });
            })
            ->get();
        return view('admin-dashboard.organizations.wallets', compact('organization', 'wallets'));
    }
} 