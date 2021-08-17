<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\RateLimitedSubscription;
use Illuminate\Console\Command;
use App\Helper\Helper;

class CheckExpiryDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expirydate:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if there are any expired subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Subscription $subscription)
    {
        $subscription->expiredButNotCanceledWithDevice()
        ->chunkById(3000, function ($activeSubscriptions) {
            $cancelSubscriptionIds = [];
            $rateLimitedSubscriptionIds = [];
            foreach ($activeSubscriptions as $activeSubscription) {
                $apiResponse = Helper::apiHttpRequest($activeSubscription->receipt, $activeSubscription->device->os);
                if($apiResponse['http-status-code'] == 429){
                    array_push($rateLimitedSubscriptionIds, ['subscription_id' => $activeSubscription->id]);
                }else if (!$apiResponse['status']) { 
                    array_push($cancelSubscriptionIds, $activeSubscription->id);
                }
            }
            Subscription::whereIn('id', $cancelSubscriptionIds)->update(['status' => 'canceled']);
            RateLimitedSubscription::insert($rateLimitedSubscriptionIds);
        });
    }
}
