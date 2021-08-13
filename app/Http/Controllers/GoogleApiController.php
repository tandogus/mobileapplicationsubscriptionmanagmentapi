<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GoogleApiController extends Controller
{
    public function googleApi(Request $request)
    {
        $receipt_last_digit = substr($request->receipt, -1);
        $receipt_last_two_digit = substr($request->receipt, -2);
        $status = false;
        if($receipt_last_two_digit % 6 == 0){
            $http_status_code = 429;
        }else if($receipt_last_digit % 2 == 1) {
            $status = true;
            $expiry_date = Carbon::now()
                    ->addYear()
                    ->format('Y-m-d\ H:i:s');
            $http_status_code = 200;
        }
        return array(
            'status' => $status,
            'expiry_date' => $expiry_date ?? '',
            'http-status-code' => $http_status_code ?? 401,
        );
    }
}
