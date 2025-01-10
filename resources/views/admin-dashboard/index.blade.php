@php
use Illuminate\Support\Facades\Auth;
@endphp

<x-admin-layout page="dashboard">
    <div class="w-full overflow-x-hidden border-t flex flex-col">
        <main class="w-full bg-body flex-grow p-6">
            <h1 class="text-3xl text-black text pb-6">Dashboard</h1>
            
            <div class="w-full text">
                <p class="text-xl pb-3">Welcome, {{ Auth::guard('admin')->user()->email }}</p>
                
                <!-- Your dashboard content goes here -->
                
            </div>
        </main>
    </div>
</x-admin-layout>
