<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
//use Auth;
use DB;

class UserAuthController extends Controller
{
    /**
    * User Add Address
    */
    public function addAddress(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->title && !$request->city && !$request->postal && !$request->line1) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $req = Validator::make($request->all(), [
                'title' => 'required|string',
                'line1' => 'required|string',
                //'line2' => 'required|string',
                'city' => 'required|string',
                'postal' => 'required|string|min:6',
            ]);

            if($req->fails()){
                return response()->json(['message' => 'Validation failed', 'data' => array()], 400);
            }

            $address = DB::table('user_address')->insert(array_merge(
                        $req->validated(),
                        ['line2' => $request->line2, 'user_id' => auth('api')->user()->id]
                    ));

            return response()->json([
                'message' => 'Address added successfully',
                'data' => array()
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }

    /**
    * User List Address
    */
    public function userAddress(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->user_id) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $req = Validator::make($request->all(), [
                'user_id' => 'required|integer',
            ]);

            if($req->fails()){
                return response()->json(['message' => 'Validation failed', 'data' => array()], 400);
            }

            $chkAnyDefaultAddress = DB::table('user_address')->where('user_id', $request->user_id)->where('default_address',1)->first();
            if(!$chkAnyDefaultAddress) {
                $ifUserAddress = DB::table('user_address')->where('user_id', $request->user_id)->whereNull('deleted_at')->first();
                if($ifUserAddress) {
                    DB::table('user_address')->where('id', $ifUserAddress->id)->update(['default_address'=>1]);        
                }                
            }

            $userAddress = DB::table('user_address')->where('user_id', $request->user_id)->whereNull('deleted_at')->get();
            if(!$userAddress) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);
            }

            $data = array();
            foreach ($userAddress as $key => $value) {
                $data[] = $value;
            }

            return response()->json([
                'message' => 'Address listed successfully',
                'data' => $data
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }

    /**
    * Make default Address
    */
    public function makeDefaultAddress(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->address_id) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $req = Validator::make($request->all(), [
                'address_id' => 'required|integer',
            ]);

            if($req->fails()){
                return response()->json(['message' => 'Validation failed', 'data' => array()], 400);
            }

            $checkAddress = DB::table('user_address')->where('id',$request->address_id)->where('user_id', auth('api')->user()->id)->whereNull('deleted_at')->first();
            if(!$checkAddress) {
                return response()->json(['message' => 'Your not authorized to make this address as default', 'data' => array()], 401);   
            }

            $userAddress = DB::table('user_address')->where('user_id', auth('api')->user()->id)->update(['default_address'=>0]);
            DB::table('user_address')->where('id', $request->address_id)->update(['default_address'=>1]);

            return response()->json([
                'message' => 'Updated successfully',
                'data' => array()
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }

    /**
    * User Edit Address
    */
    public function editAddress(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->id && !$request->title && !$request->city && !$request->postal && !$request->line1) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $req = Validator::make($request->all(), [
                'title' => 'required|string',
                'line1' => 'required|string',
                //'line2' => 'required|string',
                'city' => 'required|string',
                'postal' => 'required|string|min:6',
            ]);

            if($req->fails()){
                return response()->json(['message' => 'Validation failed', 'data' => array()], 400);
            }

            $checkAddress = DB::table('user_address')->where('id',$request->address_id)->where('user_id', auth('api')->user()->id)->whereNull('deleted_at')->first();
            if(!$checkAddress) {
                return response()->json(['message' => 'Your not authorized to edit this address', 'data' => array()], 401);   
            }

            $address = DB::table('user_address')->where('id',$request->id)->update(array_merge(
                        $req->validated(),
                        ['line2' => $request->line2]
                    ));

            return response()->json([
                'message' => 'Address edited successfully',
                'data' => array()
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);
        }
    }

    /**
    * Profile Edit
    **/
    public function profileEdit(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            $req = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100',
                //'phone' => 'required|string',
                'gender' => 'required|string|between:2,100',
            ]);

            if($req->fails()){
                return response()->json(['message' => 'Validation failed', 'data' => array()], 400);
            }

            // $checkPhone = User::where('phone', $request->phone)->whereNull('deleted_at')->whereNotIn('id',[auth('api')->user()->id])->first();
            // if($checkPhone) {
            //     return response()->json(['message' => 'Phone Number Already Exists', 'data' => array()], 400);
            // }

            $checkEmail = User::where('email', $request->email)->whereNull('deleted_at')->whereNotIn('id',[auth('api')->user()->id])->first();
            if($checkEmail) {
                return response()->json(['message' => 'Email Already Exists', 'data' => array()], 400);
            }
            $dob = $request->dob ? date('Y-m-d',strtotime($request->dob)) : NULL;
            $userDetail = array_merge( $req->validated(), ['lastname'=>$request->lastname, 'dob'=>$dob] );

            if($request->file('image')){
                $image = $this->uploadImage($request->image, '/uploads/profile/');
                if( auth('api')->user()->image && file_exists(public_path('/uploads/profile/'.auth('api')->user()->image)) ) {
                    unlink(public_path('/uploads/profile/'.auth('api')->user()->image));
                }
                $userDetail['image'] = $image;
            }

            User::where('id',auth('api')->user()->id)->update($userDetail);
            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => array()
            ], 200);    

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);
        }
    }

}
