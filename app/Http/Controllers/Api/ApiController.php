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
/**
 * @OA\Info(
 *     title="User API",
 *     version="1.0.2",
 *     description="Documentation for my RESTful API built with Laravel."
 * )
 */

class ApiController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/user/register",
     *      operationId="registerUser",
     *      tags={"Users"},
     *      summary="Register a new user",
     *      description="Registers a new user and returns the user data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="strongpassword123")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *          )
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     * )
     */
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
    
  /**
     * @OA\Post(
     *      path="/api/user/login",
     *      operationId="loginUser",
     *      tags={"Users"},
     *      summary="Login",
     *      description="Successfull Login user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="strongpassword123")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *          )
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     * )
     */
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
  /**
 * @OA\Get(
 *      path="/api/user/profile",
 *      operationId="getUserProfile",
 *      tags={"Users"},
 *      summary="Fetch User Profile",
 *      description="Endpoint to fetch user profile data.",
 *      security={{"bearerAuth": {}}},
 *      
 *     
 *      @OA\Parameter(
 *          name="Authorization",
 *          in="header",
 *          required=true,
 *          description="Bearer token for authentication",
 *          @OA\Schema(
 *              type="string",
 *              format="token",
 *              example="Bearer Add Token Key"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(
 *              @OA\Property(property="id", type="integer", example=1),
 *              @OA\Property(property="name", type="string", example="John Doe"),
 *              @OA\Property(property="email", type="string", example="johndoe@example.com")
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad request - Missing or invalid parameters",
 *          @OA\JsonContent(
 *              @OA\Property(property="error", type="string", example="Invalid request. [Provide details here]")
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized - Missing or invalid authentication token",
 *          @OA\JsonContent(
 *              @OA\Property(property="error", type="string", example="Unauthorized. [Provide details here]")
 *          )
 *      )
 * )
 */

    public function profile(){
        $request = auth()->user();
        return response()->json([
            'status' => true,
            'message' => 'Profile Information',
            'data'=> $request,
            'id' => auth()->user()->id
        ], 200);
    }
    
    // Product Module
        /**
         * @OA\Info(
         *     title="Admin API",
         *     version="1.0.0",
         *     description="Documentation for my RESTful API built with Laravel."
         * )
         */

    /**
     * @OA\Post(
     *      path="/api/user/add_product",
     *      operationId="AddProduct",
     *      tags={"Admin"},
     *      summary="Add Product",
     *      description="Admin product",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     * )
     */
    // Post Product
    public function add_product(Request $request)
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

     /**
     * @OA\Get(
     *      path="/api/user/product_list",
     *      operationId="productList",
     *      tags={"Admin"},
     *      summary="Product List",
     *      description="Admin product",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     * )
     */
    public function product_list()
    {
        return apiproducts::all();
    }

    /**
     * @OA\Get(
     *      path="/api/user/single_product",
     *      operationId="singleProduct",
     *      tags={"Admin"},
     *      summary="Single Product",
     *      description="Admin product",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     * )
     */
    public function single_product($id)
    {   
        return apiproducts::where('product_id', $id)->first();
    }

     /**
     * @OA\Delete(
     *      path="/api/user/delete_product",
     *      operationId="DeleteProduct",
     *      tags={"Admin"},
     *      summary="Delete Product",
     *      description="Admin product",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     * )
     */
    public function delete_product($id){
        $item = apiproducts::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Item deleted successfully.'], 200);
    }

     /**
     * @OA\Put(
     *      path="/api/user/update_product",
     *      operationId="update_product",
     *      tags={"Admin"},
     *      summary="Update Product",
     *      description="Admin product",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     * )
     */
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
