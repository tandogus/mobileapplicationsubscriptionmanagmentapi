<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateLimitedSubscription extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscription_id'
    ];
    public function subscription(){
        return $this->belongsTo(Subscription::class);
    }
  
}
