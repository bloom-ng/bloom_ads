<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use Illuminate\Http\Request;
use App\Http\Requests\SettingRequest;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminSetting::query();

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('key', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $adminSettings = $query->paginate(10);

        return view('admin-dashboard.settings.index', compact('adminSettings'));
    }

    public function create()
    {
        return view('admin-dashboard.settings.create');
    }

    public function store(SettingRequest $request)
    {
        AdminSetting::create($request->validated());

        return redirect()
            ->route('admin.adminsettings.index')
            ->with('success', 'Setting created successfully');
    }

    public function edit(AdminSetting $adminsetting)
    {
        return view('admin-dashboard.settings.edit', compact('adminsetting'));
    }

    public function update(SettingRequest $request, AdminSetting $adminsetting)
    {
        $adminsetting->update($request->validated());
        Cache::forget("admin_setting_{$adminsetting->key}");

        return redirect()
            ->route('admin.adminsettings.index')
            ->with('success', 'Setting updated successfully');
    }

    public function destroy(AdminSetting $adminsetting)
    {
        Cache::forget("admin_setting_{$adminsetting->key}");
        $adminsetting->delete();

        return redirect()
            ->route('admin.adminsettings.index')
            ->with('success', 'Setting deleted successfully');
    }
} 