<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\RateLimitedSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use App\Helper\Helper;

class CheckRateLimitedSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ratelimited:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if there are any rate limited subscriptions';

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
    public function handle(RateLimitedSubscription $subscription)
    {
        $subscription->withSubscriptionAndDevice()->chunkById(3000, function ($rateLimitedSubscriptions) {
            $cancelSubscriptionIds = [];
            $removeRateLimitedSubscriptionIds = [];
            foreach ($rateLimitedSubscriptions as $rateLimitedSubscription) {
                $subscription = $rateLimitedSubscription->subscription;
                $apiResponse = Helper::apiHttpRequest($subscription->receipt, $subscription->device->os);
                if (!$apiResponse['status']) { 
                    array_push($cancelSubscriptionIds, $subscription->id);
                    array_push($removeRateLimitedSubscriptionIds, $rateLimitedSubscription->id);
                }
            }
            Subscription::whereIn('id', $cancelSubscriptionIds)->update(['status' => 'canceled']);
            RateLimitedSubscription::whereIn('id', $removeRateLimitedSubscriptionIds)->delete();
        });
    }
}
