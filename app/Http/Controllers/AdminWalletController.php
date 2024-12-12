<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Carbon\Carbon;

class AdminWalletController extends Controller
{
    public function index()
    {
        $my_date = Carbon::now()->format('l, F j, Y');
        $wallets = Wallet::with(['organization', 'organization.users'])->get();
        return view('admin-dashboard.wallets.index', compact('wallets', 'my_date'));
    }
} 