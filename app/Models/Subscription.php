<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id', 'receipt', 'status','expiry_date'
    ];
    public function device(){
        return $this->belongsTo(Device::class);
    }
    public function rateLimitedSubscription(){
        return $this->hasOne(RateLimitedSubscription::class);
    }
    public function started($device_id, $receipt, $expiry_date){
        $this->create([
            'device_id' => $device_id,
            'receipt' => $receipt,
            'status' => 'started',
            'expiry_date' => $expiry_date
        ]);
    }

    public function expiredButNotCanceledWithDevice(){
        return $this->with(['device' => function ($query){
            $query->select('id', 'os');
        }])
        ->where('status', '!=', 'canceled')
        ->where('expiry_date', '<=', Carbon::now()->format('Y-m-d\ H:i:s'))
        ->select('id','device_id','receipt','status')
        ->orderBy('id');
    }
}

