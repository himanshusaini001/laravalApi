<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\apiproducts;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Add Validation Multiap type
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            // Validation All Data
            if ($validation->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validation->errors()->all(),
                ],401);
            }

            // Create All Data
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            //Response Data in Json
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'token' => $user->createToken('Api Token')->plainTextToken,
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function login(Request $request){
        try{
            $validation = Validator::make($request->all(),[
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if($validation->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validation->errors()->all(),
                ],401);
            }
            if(!Auth::attempt($request->only('email','password')))
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Emial & password Dose not match With Out record.',
                ],401);
            }
            $user = User::Where('email',$request->email)->first();
            return response()->json([
                'status' => true,
                'message' => 'User login is successfully',
                'token' => $user->createToken('Api Token')->plainTextToken,
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function profile(){
        $request = auth()->user();
        return response()->json([
            'status' => true,
            'message' => 'Profile Information',
            'data'=> $request,
            'id' => auth()->user()->id
        ], 200);
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'User logged Out',
            'data'=> [],
        ], 200);
    }
    // Product Module

    // Post Product
    public function product(Request $request)
    {
        try{
            
            // products List
            $validation = Validator::make($request->all(),[
                'pname' => 'required|string',
                'price' => 'required|integer',
                'title' => 'required|string',
                'description' => 'required|string',
                'status' => 'required|integer',
            ]);
    
            // products List Validation
            if ($validation->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validation->errors()->all(),
                ], 401);
            }
    
            // Insert products List 
            $product = apiproducts::create([
                'pname' => $request->pname, // Corrected to match validation
                'price' => $request->price,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
            ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'token' => $product->createToken('Api Token')->plainTextToken, // Corrected typo
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(), // Corrected typo
            ], 500);
        }
    }
    public function product_list()
    {
        return apiproducts::all();
    }
    public function single_product($product_id)
    {   
        return apiproducts::where('product_id', $product_id)->first();
    }
    public function delete_product($id){
        $item = apiproducts::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully.'], 200);
   
    }

    public function update_product(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'pname' => 'required|string|max:255',
            'price' => 'required|numeric',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);
    
        // Find the product by ID or fail
        $product_id = apiproducts::findOrFail($id);
    
        // Update the product details
        $product_id->update([
            'pname' => $request->pname,
            'price' => $request->price,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ]);
        
        // Return a response, e.g., the updated product
       
        return response()->json([
            'status' => true,
            'message' => 'Update Product successfully',
            'token' => $product_id->createToken('Api Token')->plainTextToken, // Corrected typo
        ], 200);
    }
    
}
