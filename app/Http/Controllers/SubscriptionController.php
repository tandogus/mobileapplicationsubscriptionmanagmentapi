<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Models\Device;
use App\Models\Subscription;
use App\Helper\Helper;

class SubscriptionController extends Controller
{
    public function subscribe(SubscriptionRequest $request, Subscription $subscription, Device $device)
    {
        $validated = $request->validated();
        $device = $device->getOsWithClientToken($validated['client_token']);
        if(!$device){
            return 'Registration not found!';
        }
        $apiResponse = Helper::apiHttpRequest($validated['receipt'], $device->os);
        if(!$apiResponse['status']){
            return 'Invalid Receipt';
        }
        if($device->subscription){
            // Same client subscribed before 
            $device->subscription->update([
                'receipt' => $validated['receipt'],
                'status' => 'renewed',
                'expiry_date' => $apiResponse['expiry_date']
            ]);
        }else{
            $subscription->started($device->id, $validated['receipt'], $apiResponse['expiry_date']);
        }
        return 'Successfully subscribed';
    }
}
