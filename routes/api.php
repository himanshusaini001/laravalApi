<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

// Register
Route::post("register",[ApiController::class,"register"]);

// Login
Route::post("login",[ApiController::class,"login"]);

//logout

Route::group([
    "middleware" =>["auth:sanctum"]
],function(){
Route::get("logout",[ApiController::class,"logout"]);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
