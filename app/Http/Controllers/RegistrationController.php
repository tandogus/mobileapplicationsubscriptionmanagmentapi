<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        //rules for validating request data
        $validated = $request->validate([
            'uid' => 'required',
            'appId' => 'required',
            'language' => 'required',
            'os' => 'required',
            'client_token' => '',
        ]);

        $registred_device = Device::select('client_token')->where('uid', $validated['uid'])
        ->where('appId', $validated['appId'])
        ->first();
        // if the client_token got deleted from client but the client registered before
        if($registred_device){
            return $registred_device->client_token;
        }
        // the uid and appId combination will be unique
        // shuffled it for security purposes
        $client_token = str_shuffle($validated['uid'].Str::random(8).$validated['appId']);
     
        Device::create([
            'uid' => $validated['uid'],
            'appId' => $validated['appId'],
            'language' => $validated['language'],
            'os' => $validated['os'],
            'client_token' => $client_token,
        ]);

        return $client_token;

    }
}
