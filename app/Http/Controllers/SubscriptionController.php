<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
    
        $validated = $request->validate([
            'client_token' => 'required',
            'receipt' => 'required',
        ]);
        $device = Device::select('os','id')->where('client_token', $validated['client_token'])->first();
        if(!$device){
            return 'Registration not found!';
        }
        if($device->os == 'IOS'){
            $apiResponse = Http::withHeaders([
                'username' => 'someUserName',
                'password' => '1234'
            ])->post(URL::to('/api/ios'),['receipt' => $validated['receipt']]);
        }else{
            $apiResponse = Http::withHeaders([
                'username' => 'someUserName',
                'password' => '1234'
            ])->post(URL::to('/api/google'),['receipt' => $validated['receipt']]);
        }
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
            Subscription::create([
                'device_id' => $device->id,
                'receipt' => $validated['receipt'],
                'status' => 'started',
                'expiry_date' => $apiResponse['expiry_date']
            ]);

        }
        return 'Successfully subscribed';
    }
}
