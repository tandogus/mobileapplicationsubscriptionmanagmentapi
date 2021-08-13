<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}

