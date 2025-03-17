<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
 //  Api
 use App\Http\Controllers\ApiController;
 use App\Http\Controllers\Api\CFSController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ApiController::class)->group(function(){
    Route::prefix('/envilog')->group(function(){
        Route::post('/gateService', 'envilogGateService');
    });
});

Route::controller(CFSController::class)->group(function(){
    Route::prefix('/cfs')->group(function(){
        Route::post('/loadBillingDev', 'loadBillingDev');
    });
}); 
