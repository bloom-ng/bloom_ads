<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::post('/login', [SignupController::class, 'login'])->name('login');

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/purchase', function () {
    return view('purchase');
});

Route::get('/service', function () {
    return view('service');
});

Route::get('/signup', function () {
    return view('signup');
});

Route::get('/signup1', [SignupController::class, 'showSignup1']);
Route::get('/signup2', [SignupController::class, 'showSignup2']);
Route::get('/signup3', [SignupController::class, 'showSignup3']);

Route::post('/signup/register', [SignupController::class, 'register'])->name('signup.register');
Route::get('/auth/{provider}', [SignupController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [SignupController::class, 'handleProviderCallback']);
Route::post('/signup/invite', [SignupController::class, 'inviteSignup'])->name('signup.invite');

Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('/forgot', function () {
    return view('forgot');
});

// Route::get('/get-started', [SignupController::class, 'index']);

// Route::post('/signup', [SignupController::class, 'store'])->name('signup');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organization/{organization}/invites', [OrganizationController::class, 'invites'])
        ->name('organization.invites');
    Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::post('/organization/{organization}/invite', [OrganizationController::class, 'invite'])
        ->name('organization.invite')
        ->middleware('can:invite,organization');
    Route::post('/organization/{organization}/invite', [OrganizationController::class, 'invite'])
        ->name('organization.invite');
    Route::delete('/organization/{organization}/invite/{invite}/cancel', [OrganizationController::class, 'cancelInvite'])
        ->name('organization.invite.cancel');
    Route::post('/organization/{organization}/invite/{invite}/resend', [OrganizationController::class, 'resendInvite'])
        ->name('organization.invite.resend');
    Route::delete('/organization/{organization}/members/{user}', [OrganizationController::class, 'removeMember'])
        ->name('organization.members.remove');
    Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/organization', [SettingsController::class, 'setCurrentOrganization'])->name('settings.set-organization');
    Route::post('/settings/wallet', [SettingsController::class, 'createWallet'])->name('settings.create-wallet');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet', [WalletController::class, 'create'])->name('wallet.create');
    Route::post('/wallet/fund/paypal', [WalletController::class, 'fundWithPaypal'])->name('wallet.fund.paypal');
    Route::post('/wallet/fund/flutterwave', [WalletController::class, 'fundWithFlutterwave'])->name('wallet.fund.flutterwave');
    Route::post('/wallet/fund/paystack', [WalletController::class, 'fundWithPaystack'])->name('wallet.fund.paystack');

    // Payment callbacks
    Route::get('/wallet/fund/flutterwave/callback', [WalletController::class, 'handleFlutterwaveCallback'])
        ->name('wallet.fund.flutterwave.callback');
    Route::get('/wallet/fund/paystack/callback', [WalletController::class, 'handlePaystackCallback'])
        ->name('wallet.fund.paystack.callback');
    Route::get('/wallet/fund/paypal/success', [WalletController::class, 'handlePaypalSuccess'])
        ->name('wallet.fund.paypal.success');
    Route::get('/wallet/fund/paypal/cancel', [WalletController::class, 'handlePaypalCancel'])
        ->name('wallet.fund.paypal.cancel');
});
