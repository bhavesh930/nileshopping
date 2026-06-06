<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
//use Auth;

class JWTAuthController extends Controller
{
    //
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'mobileLogin']]);
    }

    /**
    * Get a JWT via given credentials.
    */
    public function login(Request $request){
        $req = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:5',
        ]);

        if ($req->fails()) {
            return response()->json($req->errors(), 422);
        }

        if (! $token = auth('api')->attempt($req->validated())) {
            return response()->json(['Auth error' => 'Unauthorized'], 401);
        }

        return $this->generateToken($token);
    }

    /**
    * Sign up.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function register(Request $request) {
        try {
            $req = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users',
                //'password' => 'required|string|confirmed|min:6',
                'phone' => 'required|string|unique:users',
                'gender' => 'required|string|between:2,100',
            ]);

            if($req->fails()){
                return response()->json([
                    'message' => 'Validation failed', 
                    'errors' => $req->errors()
                ], 400);
            }
            $userData = User::where('phone', $request->phone)->whereNull('deleted_at')->first();
            if($userData) {
                return response()->json(['message' => 'Already registered', 'data' => $userData], 400);   
            }
            //'password' => Hash::make($request->password), 
            //Setting fix password for App users for jwt token.
            //bcrypt($request->password)
            $dob = $request->dob ? date('Y-m-d',strtotime($request->dob)) : NULL;
            $user = User::create(array_merge(
                        $req->validated(),
                        ['password' => Hash::make('Nile@2021'), 'menuroles'=>'user', 'lastname'=>$request->lastname, 'phone'=>$request->phone, 'gender'=>$request->gender, 'dob'=>$dob]
                    ));

            return response()->json([
                'message' => 'Registered Successfully',
                'data' => $user
            ], 201);   
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);
        }
    }

    /**
    * Sign out
    */
    public function signout() {
        auth('api')->logout();
        return response()->json(['message' => 'User loged out']);
    }

    /**
    * Token refresh
    */
    public function refresh() {
        return $this->generateToken(auth('api')->refresh());
    }

    /**
    * User, this api is used to get logged in user detail from token so don't make any changes here if need create a new API for user profile as this method is used inside other api to return detail of logged in user as an object.
    */
    public function user() {
        return response()->json(auth('api')->user());
    }

    /**
    * Generate token
    */
    protected function generateToken($token){
        /*return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);*/

        return array('access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth('api')->factory()->getTTL() * 60);
    }

    /**
    * Check Mobile Registered, if registered login else register
    *
    */
    public function mobileLogin(Request $request){
        try {
            if(!$request->phone) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $userData = User::where('phone', $request->phone)->whereNull('deleted_at')->first();
            if(!$userData) {
                return response()->json(['message' => 'Not registered', 'data' => array()], 404);   
            }

            if (! $token = auth('api')->attempt(['email'=>$userData->email, 'password'=>'Nile@2021'])) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }
            //return $this->generateToken($token);
            $userData['image'] = ($userData->image) ? url('/').'/uploads/profile/'.$userData->image : '';
            $userData['Authorization'] = $this->generateToken($token);
            return response()->json([
                'message' => 'Logged-in',
                'data' => $userData
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }

}
