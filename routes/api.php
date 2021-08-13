<?php

use App\Http\Controllers\CheckSubscriptionController;
use App\Http\Controllers\GoogleApiController;
use App\Http\Controllers\IOSApiController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [RegistrationController::class, 'register']);
Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
Route::post('/check-subscription', [CheckSubscriptionController::class, 'checkSubscription']);



// Google and IOS Mock api
Route::post('/google' ,[GoogleApiController::class, 'googleApi']);
Route::post('/ios' ,[IOSApiController::class, 'iosApi']);