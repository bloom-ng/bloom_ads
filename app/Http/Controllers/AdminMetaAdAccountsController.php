<?php

namespace App\Http\Controllers;

use App\Services\Meta\AdAccountService as MetaAdAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminMetaAdAccountsController extends Controller
{
    protected $metaAdAccount;

    public function __construct(MetaAdAccountService $metaAdAccount)
    {
        $this->metaAdAccount = $metaAdAccount;
    }

    public function index(Request $request)
    {
        try {
            $tab = $request->get('tab', 'owned');
            $limit = $request->get('limit', 15);
            $before = $request->get('before');
            $after = $request->get('after');
            
            $result = $tab === 'owned' 
                ? $this->metaAdAccount->getOwnedAdAccounts($before, $after, $limit)
                : $this->metaAdAccount->getClientAdAccounts($before, $after, $limit);
            
            $adAccounts = collect($result->items)->map(
                fn ($account) => $this->transformAdAccount($account)
            );

            Log::info('Meta Ad Accounts Retrieved:', [
                'type' => $tab,
                'count' => $result->count,
                'has_more' => $result->hasMore(),
                'before' => $before,
                'after' => $after
            ]);

            return view('admin-dashboard.meta.index', [
                'adAccounts' => $adAccounts,
                'nextCursor' => $result->nextCursor,
                'prevCursor' => $result->previousCursor,
                'hasNext' => $result->next,
                'hasPrev' => $result->previous,
                'activeTab' => $tab
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching Meta ad accounts:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('admin-dashboard.meta.index', [
                'adAccounts' => collect([])
            ])->with('error', 'Unable to fetch Meta ad accounts. Please try again later.');
        }
    }

    public function transformAdAccount(array $account): array
    {
        return [
            'id' => str_replace('act_', '', $account['id']),
            'account_id' => $account['id'],
            'name' => $account['name'],
            'currency' => $account['currency'],
            'status' => $account['account_status'] === 1 ? 'ACTIVE' : 'INACTIVE',
            'amount_spent' => $account['amount_spent'],
            'balance' => $account['balance'],
            'business_name' => $account['business']['name'] ?? null,
        ];
    }

    public function list(Request $request)
    {
        try {
            $tab = $request->get('tab', 'owned');
            $limit = min($request->get('limit', 30), 100); // Cap at 100
            $before = $request->get('before');
            $after = $request->get('after');
            
            $result = $tab === 'owned' 
                ? $this->metaAdAccount->getOwnedAdAccounts($before, $after, $limit)
                : $this->metaAdAccount->getClientAdAccounts($before, $after, $limit);

            return response()->json([
                $tab => collect($result->items)->map(
                    fn ($account) => $this->transformAdAccount($account)
                ),
                'nextCursor' => $result->nextCursor,
                'previousCursor' => $result->previousCursor,
                'hasNext' => $result->hasMore(),
                'hasPrev' => !empty($result->previousCursor)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch Meta accounts'], 500);
        }
    }
} 