<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\FlutterwaveService;

class FlutterwaveWebhookController extends Controller
{
    protected $flutterwaveService;

    public function __construct(FlutterwaveService $flutterwaveService)
    {
        $this->flutterwaveService = $flutterwaveService;
    }

    public function handleTransferWebhook(Request $request)
    {
        // Log the webhook payload
        Log::info('Flutterwave Transfer Webhook received:', $request->all());

        // Verify the webhook signature
        $signature = $request->header('verif-hash');
        if (!$this->flutterwaveService->verifyWebhookSignature($signature, $request->getContent())) {
            Log::warning('Invalid webhook signature');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        // Process the transfer status
        $data = $request->all();
        if ($data['event'] === 'transfer.completed') {
            // Handle successful transfer
            Log::info('Transfer completed successfully:', [
                'reference' => $data['data']['reference'],
                'status' => $data['data']['status'],
                'amount' => $data['data']['amount']
            ]);
        } elseif ($data['event'] === 'transfer.failed') {
            // Handle failed transfer
            Log::error('Transfer failed:', [
                'reference' => $data['data']['reference'],
                'status' => $data['data']['status'],
                'complete_message' => $data['data']['complete_message'] ?? null
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
