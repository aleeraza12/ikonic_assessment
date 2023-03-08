<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        $user = new User([
            'email' => $email,
            'name' => $name,
            'password' => bcrypt(str_random(32)), // generate a random password
            'user_type' => User::TYPE_AFFILIATE // set user type to affiliate
        ]);
        $user->save();

        $affiliate = new Affiliate([
            'commission_rate' => $commissionRate,
        ]);
        $affiliate->user()->associate($user);
        $affiliate->merchant()->associate($merchant);
        $affiliate->save();

        Mail::to($email)->send(new AffiliateCreated($affiliate));

        return $affiliate;

    }
}
