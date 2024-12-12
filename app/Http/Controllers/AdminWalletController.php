<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Carbon\Carbon;

class AdminWalletController extends Controller
{
    public function index()
    {
        $my_date = Carbon::now()->format('l, F j, Y');
        
         // Add pagination while maintaining the eager loading of relationships
         $wallets = Wallet::with(['organization', 'organization.users'])
         ->orderBy('created_at', 'desc')  // Sort by newest first
         ->paginate(10);  // 10 wallets per page
     
     return view('admin-dashboard.wallets.index', compact('wallets', 'my_date'));
 }
}