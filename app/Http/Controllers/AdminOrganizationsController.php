<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class AdminOrganizationsController extends Controller
{
    public function index()
    {
        $organizations = Organization::with(['user', 'users'])->get();
        return view('admin-dashboard.organizations.index', compact('organizations'));
    }

    public function show(Organization $organization)
    {
        $adAccounts = $organization->adAccounts()->with(['user'])->get();
        return view('admin-dashboard.organizations.show', compact('organization', 'adAccounts'));
    }
} 