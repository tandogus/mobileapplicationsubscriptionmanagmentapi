<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid','appId','language','os','client_token'
    ];
    
    public function subscription(){
        return $this->hasOne(Subscription::class);
    }

    public function getDeviceFromClientToken($client_token){
        return $this->where('client_token', $client_token)->first();
    }

    public function getClientTokenWithUIdAndAppId($uid, $appId){
        return $this->select('client_token')
            ->where('uid', $uid)
            ->where('appId', $appId)
            ->first();
    }
    public function createWithClientToken($device_data){
        $device_data['client_token'] = str_shuffle($device_data['uid'].Str::random(8).$device_data['appId']);
        $this->create($device_data);
        return $device_data['client_token']; 
    }

    public function getOsWithClientToken($client_token){
        return $this->select('os','id')->where('client_token', $client_token)->first();
    }
}
