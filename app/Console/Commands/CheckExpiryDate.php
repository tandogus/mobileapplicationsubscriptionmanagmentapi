<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\RateLimitedSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

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
    public function handle()
    {
        Subscription::with(['device' => function ($query){
            $query->select('id', 'os');
        }])
        ->where('status', '!=', 'canceled')
        ->where('expiry_date', '<=', Carbon::now()->addYear()->format('Y-m-d\ H:i:s'))
        ->select('id','device_id','receipt','status')
        ->orderBy('id')
        ->chunkById(3000, function ($activeSubscriptions) {
            $cancelSubscriptionIds = [];
            $rateLimitedSubscriptionIds = [];
            foreach ($activeSubscriptions as $activeSubscription) {
                if (strtoupper($activeSubscription->device->os) == 'IOS') {
                    $apiResponse = Http::withHeaders([
                        'username' => 'someUserName',
                        'password' => '1234'
                    ])->post(URL::to('/api/ios'), ['receipt' => $activeSubscription->receipt]);
                } else {
                    $apiResponse = Http::withHeaders([
                        'username' => 'someUserName',
                        'password' => '1234'
                    ])->post(URL::to('/api/google'), ['receipt' => $activeSubscription->receipt]);
                }
                if (!$apiResponse['status']) { 
                    array_push($cancelSubscriptionIds, $activeSubscription->id);
                }else if($apiResponse['http-status-code'] == 429){
                    array_push($rateLimitedSubscriptionIds, ['subscription_id' => $activeSubscription->id]);
                }
            }
            Subscription::whereIn('id', $cancelSubscriptionIds)->update(['status' => 'canceled']);
            RateLimitedSubscription::insert($rateLimitedSubscriptionIds);
        });
    }
}
