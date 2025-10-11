<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\NewsController;

Route::get('/test', function () {
    return response()->json(['status'=>'success','message'=>'API route working!']);
});

Route::apiResource('events', EventController::class);
Route::apiResource('news', NewsController::class);
