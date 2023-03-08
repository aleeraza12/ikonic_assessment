<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        $merchant = $request->user()->merchant;

        $from = $request->input('from');
        $to = $request->input('to');

        $count = $this->merchantService->countOrdersInRange($merchant, $from, $to);
        $commissionOwed = $this->merchantService->calculateUnpaidCommissions($merchant, $from, $to);
        $revenue = $this->merchantService->sumOrderSubtotals($merchant, $from, $to);

        return response()->json([
            'count' => $count,
            'commission_owed' => $commissionOwed,
            'revenue' => $revenue,
        ]);
    }
}



