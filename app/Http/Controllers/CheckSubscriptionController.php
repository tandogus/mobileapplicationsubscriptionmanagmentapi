<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Device;
use Illuminate\Http\Request;

class CheckSubscriptionController extends Controller
{
    public function checkSubscription(Request $request){
        $validated = $request->validate([
            'client_token' => 'required'
        ]);
        $device = Device::where('client_token', $validated['client_token'])->first();
        if(!$device){
            return 'Unregistered Device!';
        }
    
        if(!$device->subscription){
            return 'Unable to find subscription with provided client_token';
        }
    
        return $device->subscription->status;
    }
}
