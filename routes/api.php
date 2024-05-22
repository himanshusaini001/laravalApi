<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;
use Whoops\Run;

// Register
Route::post("user/register",[ApiController::class,"register"]);

// Login
Route::post("user/login",[ApiController::class,"login"]);

Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum', 'admin']], function () {

    // User Profile 
    Route::get("profile", [ApiController::class,"profile"]);

    // Logout
    Route::get("logout", [ApiController::class,"logout"]);
});

// Product Route
 Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum', 'admin']], function () {

    // Add Product Data
    Route::post("add_product",[ApiController::class,"add_product"]);

    // Get All Product Data
    Route::get('product_list', [ApiController::class, 'product_list']);

    // Update Product Data
    Route::put("update_product/{id}",[ApiController::class,"update_product"]);
    
    //Delete Product Data
    Route::delete('delete_product/{id}', [ApiController::class, 'delete_product']);
});


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
