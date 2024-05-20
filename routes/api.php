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

    // Get All Product Data
    Route::get('get_product_list', [ApiController::class, 'get_product_list']);
    
    // Get Single Product Data
    Route::get('get_single_data/{product_id}', [ApiController::class, 'get_single_data']);

      //Delete Product Data
      Route::get('delete_product_data/{id}', [ApiController::class, 'delete_product_data']);
});




// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
