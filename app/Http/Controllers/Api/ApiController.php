<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    // public function logout(){
    //     dd(auth()->user());
    //     auth()->user()->token()->delete();
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'User logged Out',
    //         'data'=> [],
    //     ], 200);
    // }
    
}
