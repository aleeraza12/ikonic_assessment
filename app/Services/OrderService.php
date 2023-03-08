<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // Check if the order has already been processed based on order_id
        $order = Order::where('order_id', $data['order_id'])->first();
        if ($order) {
            return;
        }

        // Get the merchant associated with the order
        $merchant = Merchant::where('domain', $data['merchant_domain'])->first();
        if (!$merchant) {
            // Merchant not found, do something like throw an exception or log an error
            return;
        }

        // Check if an affiliate already exists for the customer's email
        $affiliate = Affiliate::where('email', $data['customer_email'])->first();
        if (!$affiliate) {
            // Create a new affiliate
            $affiliate = $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], 0);
        }

        // Create a new order and associate it with the affiliate and merchant
        $order = new Order();
        $order->order_id = $data['order_id'];
        $order->subtotal_price = $data['subtotal_price'];
        $order->discount_code = $data['discount_code'];
        $order->affiliate()->associate($affiliate);
        $order->merchant()->associate($merchant);
        $order->save();

        // Log the commission for the affiliate
        $commission = $data['subtotal_price'] * $merchant->commission_rate;
        $affiliate->logCommission($commission);
    }
}
