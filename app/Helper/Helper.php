<?php

namespace App\Helper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
class Helper{

    public static function apiHttpRequest($receipt, $os){
        if(strtoupper($os) == 'IOS'){
            $response = Http::withHeaders([
                'username' => 'someUserName',
                'password' => '1234'
            ])->post(URL::to('/api/ios'),['receipt' => $receipt]);
        }else{
            $response = Http::withHeaders([
                'username' => 'someUserName',
                'password' => '1234'
            ])->post(URL::to('/api/google'),['receipt' => $receipt]);
        }
        return $response;
    }
}