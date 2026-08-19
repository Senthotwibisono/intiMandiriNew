<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\cesa\FrontController;
use App\Http\Controllers\cesa\BackController;

Route::prefix('/cesa/dokumen')->name('cesa.dokumen.')->controller(BackController::class)->group(function(){
    Route::post('/plp', 'plpOnDemand')->name('plp');
    Route::post('/sppb', 'sppbOnDemand')->name('sppb');
    Route::post('/bc23', 'bc23OnDemand')->name('bc23');
    Route::post('/pabean', 'pabeanOnDemand')->name('pabean');
    Route::post('/spjm', 'spjmOnDemand')->name('spjm');
});