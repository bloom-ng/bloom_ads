<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AdAccountController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\FlutterwaveWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');





Route::middleware('auth:admin')->group(function () {
    Route::get('/organizations', [OrganizationController::class, 'index']);
    Route::get('/organizations/{organization}/ad-accounts', [AdAccountController::class, 'getOrganizationAccounts']);
    Route::post('/ad-accounts/link', [AdAccountController::class, 'linkAccount']);
}); 

// Route::middleware('auth')->group(function () {
    Route::get('/ad-accounts/{adAccount}/spend-cap', [AdAccountController::class, 'getSpendCap'])->name('api.ad-accounts.spend-cap');
// }); 

// Flutterwave Webhooks
Route::post('/webhooks/flutterwave/transfer', [FlutterwaveWebhookController::class, 'handleTransferWebhook'])
    ->name('webhooks.flutterwave.transfer');
