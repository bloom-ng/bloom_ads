<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdmindashController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AdminWalletController;
use App\Http\Controllers\AdAccountsController;
use App\Http\Controllers\AdminAdAccountsController;
use App\Http\Controllers\AdminOrganizationsController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminRockAdsAccountsController;
use App\Http\Controllers\AdminMetaAdAccountsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\User;

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

// Guest admin routes (for login)
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.authenticate');
});


// Admin protected routes
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdmindashController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');

    Route::get('/wallets', [AdminWalletController::class, 'index'])->name('wallets.index');

    Route::get('/adaccounts', [AdminAdAccountsController::class, 'index'])->name('adaccounts.index');
    Route::get('/adaccounts/{adAccount}/edit', [AdminAdAccountsController::class, 'edit'])->name('adaccounts.edit');
    Route::put('/adaccounts/{adAccount}', [AdminAdAccountsController::class, 'update'])->name('adaccounts.update');
    Route::delete('/adaccounts/{adAccount}', [AdminAdAccountsController::class, 'destroy'])->name('adaccounts.destroy');
    Route::get('/adaccounts', [AdminAdAccountsController::class, 'index'])->name('adaccounts.index');
    Route::get('/adaccounts/export/processing', [AdminAdAccountsController::class, 'exportProcessingAccounts'])
        ->name('adaccounts.export.processing');

    Route::get('/adaccounts/export/filtered', [AdminAdAccountsController::class, 'exportFilteredAccounts'])
        ->name('adaccounts.export.filtered');

    Route::get('/organizations', [AdminOrganizationsController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/{organization}', [AdminOrganizationsController::class, 'show'])->name('organizations.show');
    Route::get('/organizations/{organization}/members', [AdminOrganizationsController::class, 'members'])
        ->name('organizations.members');

    Route::resource('adminsettings', AdminSettingsController::class);
    Route::post('adminsettings/{adminSetting}/update-value', [AdminSettingsController::class, 'updateValue'])->name('adminsettings.update-value');

    // Add this new route for RockAds accounts
    Route::get('/rockads-accounts', [AdminRockAdsAccountsController::class, 'index'])->name('rockads.accounts.index');

    Route::get('/meta/accounts', [AdminMetaAdAccountsController::class, 'index'])
        ->name('meta.accounts.index');

    // Add these routes in your admin group
    Route::get('/meta-accounts/list', [AdminMetaAdAccountsController::class, 'list'])
        ->name('admin.meta-accounts.list');
    Route::post('/adaccounts/link-meta', [AdminAdAccountsController::class, 'linkMeta'])
        ->name('admin.adaccounts.link-meta');
    Route::post('/adaccounts/unlink-meta', [AdminAdAccountsController::class, 'unlinkMeta'])
        ->name('admin.adaccounts.unlink-meta');

    Route::post('/wallets/{wallet}/credit', [AdminWalletController::class, 'credit'])->name('wallets.credit');
    Route::post('/wallets/{wallet}/debit', [AdminWalletController::class, 'debit'])->name('wallets.debit');
    Route::post('/ad-accounts/{adAccount}/transfer', [AdminWalletController::class, 'transferFromAdAccount'])->name('adaccounts.transfer');
});

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

Route::middleware(['auth', 'verified'])->group(function () {
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
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.2fa-update');
    
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet', [WalletController::class, 'create'])->name('wallet.create');
    Route::post('/wallet/fund/paypal', [WalletController::class, 'fundWithPaypal'])->name('wallet.fund.paypal');
    Route::post('/wallet/fund/flutterwave', [WalletController::class, 'fundWithFlutterwave'])->name('wallet.fund.flutterwave');
    Route::post('/wallet/fund/paystack', [WalletController::class, 'fundWithPaystack'])->name('wallet.fund.paystack');

    Route::get('/dashboard/adaccounts', [AdAccountsController::class, 'index'])
        ->name('adaccounts.index');
    Route::get('/dashboard/adaccounts/create', [AdAccountsController::class, 'create'])
        ->name('adaccounts.create')
        ->middleware(\App\Http\Middleware\CheckRockAdsConfig::class);
    Route::post('/dashboard/adaccounts', [AdAccountsController::class, 'store'])
        ->name('adaccounts.store');
    Route::get('/dashboard/adaccounts/{adAccount}/edit', [AdAccountsController::class, 'edit'])->name('adaccounts.edit');
    Route::put('/dashboard/adaccounts/{adAccount}', [AdAccountsController::class, 'update'])->name('adaccounts.update');
    Route::delete('/dashboard/adaccounts/{adAccount}', [AdAccountsController::class, 'destroy'])->name('adaccounts.destroy');
    Route::get('/dashboard/adaccounts/{adAccount}', [AdAccountsController::class, 'show'])
        ->name('adaccounts.show');

    // Payment callbacks
    Route::get('/wallet/fund/flutterwave/callback', [WalletController::class, 'handleFlutterwaveCallback'])
        ->name('wallet.fund.flutterwave.callback');
    Route::get('/wallet/fund/paystack/callback', [WalletController::class, 'handlePaystackCallback'])
        ->name('wallet.fund.paystack.callback');
    Route::get('/wallet/fund/paypal/success', [WalletController::class, 'handlePaypalSuccess'])
        ->name('wallet.fund.paypal.success');
    Route::get('/wallet/fund/paypal/cancel', [WalletController::class, 'handlePaypalCancel'])
        ->name('wallet.fund.paypal.cancel');

    // Add this with your other user routes
    Route::post('/logout', [UsersController::class, 'logout'])->name('user.logout');

    // Add this with your other wallet routes
    Route::post('/wallet/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer');

    // Inside the authenticated routes group
    Route::post('/dashboard/adaccounts/{adAccount}/deposit', [AdAccountsController::class, 'deposit'])
        ->name('adaccounts.deposit');
    Route::post('/dashboard/adaccounts/{adAccount}/withdraw', [AdAccountsController::class, 'withdraw'])
        ->name('adaccounts.withdraw');

    // User account management
    Route::get('/account', [UsersController::class, 'account'])->name('account');
    Route::put('/account/update', [UsersController::class, 'updateProfile'])->name('account.update');

});

// Add these routes for email verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard')->with('verified', true);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Password Reset Routes
Route::get('/forgot-password', function () {
    return view('forgot');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token, Request $request) {
    return view('auth.reset-password', ['token' => $token, 'request' => $request]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');
