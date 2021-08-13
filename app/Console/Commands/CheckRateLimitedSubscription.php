<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\RateLimitedSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

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
    public function handle()
    {
        RateLimitedSubscription::with([
            'subscription' => function($query){
            $query->select('id','device_id','receipt','status');
        }])->with(['subscription.device' => function($query){
            $query->select('id', 'os');
        }])->chunkById(3000, function ($rateLimitedSubscriptions) {
            $cancelSubscriptionIds = [];
            $removeRateLimitedSubscriptionIds = [];
            foreach ($rateLimitedSubscriptions as $rateLimitedSubscription) {
                $subscription = $rateLimitedSubscription->subscription;
                if (strtoupper($subscription->device->os) == 'IOS') {
                    $apiResponse = Http::withHeaders([
                        'username' => 'someUserName',
                        'password' => '1234'
                    ])->post(URL::to('/api/ios'), ['receipt' => $subscription->receipt]);
                } else {
                    $apiResponse = Http::withHeaders([
                        'username' => 'someUserName',
                        'password' => '1234'
                    ])->post(URL::to('/api/google'), ['receipt' => $subscription->receipt]);
                }
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
