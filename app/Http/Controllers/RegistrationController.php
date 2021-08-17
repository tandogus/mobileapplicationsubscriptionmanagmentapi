<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Device;


class RegistrationController extends Controller
{
    public function register(RegisterRequest $request, Device $device)
    {
        $validated = $request->validated();
        $registred_device = $device->getClientTokenWithUIdAndAppId($validated['uid'], $validated['appId']);
        if($registred_device){
            return $registred_device->client_token;
        }
        $client_token = $device->createWithClientToken($validated);
        return $client_token;

    }
}
