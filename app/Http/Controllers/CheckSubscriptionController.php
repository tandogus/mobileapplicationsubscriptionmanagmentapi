<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Device;
use Illuminate\Http\Request;

class CheckSubscriptionController extends Controller
{
    public function checkSubscription(Request $request, Device $device){
        $validated = $request->validated();
        $device->getDeviceFromClientToken($validated['client_token']);
        if(!$device){
            return 'Unregistered Device!';
        }
    
        if(!$device->subscription){
            return 'Unable to find subscription with provided client_token';
        }
    
        return $device->subscription->status;
    }
}
