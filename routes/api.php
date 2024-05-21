<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;
use Whoops\Run;

// Register
Route::post("register",[ApiController::class,"register"]);

// Login
Route::post("login",[ApiController::class,"login"]);

//logout

Route::group(["middleware" => ["auth:sanctum",'admin']], function () {

    // single Data Fetch
    Route::get("profile", [ApiController::class,"profile"]);

    // Logout
    Route::get("logout", [ApiController::class,"logout"]);
    // Product Data

    // Post Product Data
    Route::post("post_product",[ApiController::class,"post_product"]);

    
    // Post Product Data
    Route::put("update_product/{id}",[ApiController::class,"update_product"]);

    // Post Product Data
    Route::put("update_product/{id}",[ApiController::class,"update_product"]);

    // Get All Product Data
    Route::get('product_list', [ApiController::class, 'product_list']);
    
    //Delete Product Data
    Route::delete('delete_product/{id}', [ApiController::class, 'delete_product']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
