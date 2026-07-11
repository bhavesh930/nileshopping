<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
use App\Models\Seller;
use App\Models\Category;
use App\Models\Brand;
//use Auth;
use DB;

class ListingAuthController extends Controller
{
    /**
    * Product List
    */
    public function getCategoryListing(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }
            $categoryId = $request->category_id;
            $query = DB::table('listings')->whereIn('menu_id', function($query) use ($categoryId)
                                                            {
                                                                $query->select('menus.id')->from('menus')
                                                                      ->where('menus.id', $categoryId)
                                                                      ->orWhere('menus.parent_id', $categoryId);
                                                            })
                                        //->where('menu_id', $request->category_id)
                                        ->where('status', 3)->whereNull('deleted_at');
            $offset = $request->offset;
            if(!$offset) {
                $offset = 0;
            }
            $limit = $request->limit ? $request->limit : 10; 
            $categoryList = $query->offset($offset)->limit($limit)->get();
            if(empty($categoryList)) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);   
            }
            $nextOffset = count($categoryList) + $offset;
            $data = array();
            foreach ($categoryList as $key => $value) {
                $userData = User::where('id', $value->user_id)->first();
                $categoryData = Category::where('id', $value->category_id)->first();
                $brandData = Brand::where('brand_id', $value->brand_id)->first();
                $listingdata = DB::table('listingdatas')->where('listing_id', $value->id)->where('status', 1)->whereNull('deleted_at')->first();
                $listingdata->product_id = $listingdata->id;
                $listingdata->description = NULL;
                $listingdata->category_name = $categoryData->name;
                $listingdata->hastags = $value->hastags;
                $listingdata->brand_name = $brandData->brand_name;
                $listingPhotos = DB::table('listingphotos')->where('listing_id', $value->id)->whereNull('deleted_at')->get();
                
                $chkWhistlist = DB::table('whishlist')->where('user_id', auth('api')->user()->id)->where('listing_id',$value->id)->whereNull('deleted_at')->first();
                $listingdata->isFavorite = $chkWhistlist ? true : false;
                $avgProductRating = DB::table('reviews')->where('product_id', $listingdata->id)->avg('rating');
                $listingdata->rating = $avgProductRating ? number_format($avgProductRating, 1) : number_format(0, 1);
                $listingdata->actualPrice = number_format($listingdata->mrp, 2);
                $listingdata->discount = number_format(0, 2);

                $cartQuery = DB::table('carts')->where('user_id', auth('api')->user()->id)->where('product_id', $listingdata->id)->whereNull('deleted_at')->first();
                $listingdata->cart_id = $cartQuery->id ?? 0;

                $image = array();
                if(!empty($listingPhotos)) {
                    foreach ($listingPhotos as $pkey => $pvalue) {
                        if($pvalue->image_1) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_1;
                        }
                        if($pvalue->image_2) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_2;   
                        }
                        if($pvalue->image_3) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_3;
                        }
                        if($pvalue->image_4) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_4;
                        }
                        if($pvalue->image_5) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_5;
                        }
                    }
                }
                $listingdata->images = $image;
                $listingdata->offset = $nextOffset;

                $sellerInfo = User::where('id', $value->user_id)->get();
                $sellerData = array();
                if($sellerInfo) {
                    foreach($sellerInfo as $sValue) {
                        $seller['sellerName'] = $sValue->name;
                        $seller['image'] = ($sValue->image) ? url('/').'/uploads/profile/'.$sValue->image : '';
                        $seller['distance'] = number_format(0, 1);
                        $seller['rating'] = number_format(0, 1);

                        $sellerData = $seller;
                    }
                }
                $listingdata->seller = $sellerData;

                $data[] = $listingdata;
            }

            if(empty($data)) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);   
            }
            
            return response()->json([
                'message' => 'Product listed successfully',
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
     * Particular Product Detail
     **/
    public function productDetail(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }
            
            $listingdata = DB::table('listingdatas')->where('id', $request->product_id)->where('status', 1)->whereNull('deleted_at')->first();
            if(!$listingdata) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);
            }

            $listingdata->product_id = $listingdata->id;
            $listingdata->description = NULL;
            $chkWhistlist = DB::table('whishlist')->where('user_id', auth('api')->user()->id)->where('listing_id',$listingdata->listing_id)->whereNull('deleted_at')->first();
            $listingdata->isFavorite = $chkWhistlist ? true : false;
            $avgProductRating = DB::table('reviews')->where('product_id', $listingdata->id)->avg('rating');
            $listingdata->rating = $avgProductRating ? number_format($avgProductRating, 1) : number_format(0, 1);
            $listingdata->actualPrice = number_format($listingdata->mrp, 2);
            $listingdata->discount = number_format(0, 2);

            $cartQuery = DB::table('carts')->where('user_id', auth('api')->user()->id)->where('product_id', $listingdata->id)->where('status', 1)->whereNull('deleted_at')->first();
            $listingdata->cart_id = $cartQuery->id ?? 0;

            $listingPhotos = DB::table('listingphotos')->where('listing_id', $listingdata->listing_id)->whereNull('deleted_at')->get();

            $image = array();
            if(!empty($listingPhotos)) {
                foreach ($listingPhotos as $pkey => $pvalue) {
                    if($pvalue->image_1) {
                        $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_1;
                    }
                    if($pvalue->image_2) {
                        $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_2;   
                    }
                    if($pvalue->image_3) {
                        $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_3;
                    }
                    if($pvalue->image_4) {
                        $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_4;
                    }
                    if($pvalue->image_5) {
                        $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_5;
                    }
                }
            }
            $listingdata->images = $image;
            
            $sellerInfo = DB::table('listings')->select('listings.*', 'users.name', 'users.lastname', 'users.image')->leftJoin('users', 'listings.user_id', '=', 'users.id')->where('listings.id', $listingdata->listing_id)->whereNull('listings.deleted_at')->get();
            $sellerData = array();
            if($sellerInfo) {
                foreach($sellerInfo as $sValue) {
                    $seller['sellerName'] = $sValue->name;
                    $seller['image'] = ($sValue->image) ? url('/').'/uploads/profile/'.$sValue->image : '';
                    $seller['distance'] = number_format(0, 1);
                    $seller['rating'] = number_format(0, 1);

                    $sellerData = $seller;
                }
            }
            $listingdata->seller = $sellerData;
            $listingdata->cart_detail = $cartQuery;

            // Track recently viewed (feeds the homeScreen recentlyViewedProducts section)
            $currentUserId = auth('api')->id();
            if ($currentUserId && $listingdata->listing_id) {
                $rvExists = DB::table('recently_viewed')
                    ->where('user_id', $currentUserId)
                    ->where('listing_id', $listingdata->listing_id)
                    ->exists();

                if ($rvExists) {
                    DB::table('recently_viewed')
                        ->where('user_id', $currentUserId)
                        ->where('listing_id', $listingdata->listing_id)
                        ->update(['updated_at' => now()]);
                } else {
                    DB::table('recently_viewed')->insert([
                        'user_id'    => $currentUserId,
                        'listing_id' => $listingdata->listing_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'message' => 'Listed successfully',
                'data' => $listingdata
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }

    /**
    * User Add Product WhishList
    */
    public function addProductWhishList(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->listing_id) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $chkWhish = DB::table('whishlist')->where('listing_id',$request->listing_id)->where('user_id',auth('api')->user()->id)->whereNull('deleted_at')->first();
            if(!$chkWhish) {
                DB::table('whishlist')->insert(['listing_id' => $request->listing_id, 'user_id' => auth('api')->user()->id]);
                $message = 'Product whishlist successfully';
            }

            if($chkWhish) {
                DB::table('whishlist')->where('id', $chkWhish->id)->delete();
                $message = 'Product whishlist removed successfully';
            }

            return response()->json([
                'message' => $message,
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
     * WhishList
     */
    public function whishList(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            $query = DB::table('whishlist')->where('user_id', auth('api')->user()->id)->whereNull('deleted_at');

            $offset = $request->offset;
            if(!$offset) {
                $offset = 0;
            }
            $limit = $request->limit ? $request->limit : 10;
            
            $chkWhish = $query->offset($offset)->limit($limit)->get();
            if(count($chkWhish) == 0) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);
            }
            $nextOffset = count($chkWhish) + $offset;
            $data = array();
            foreach ($chkWhish as $key => $value) {
                $listingdata = DB::table('listingdatas')->where('listing_id', $value->listing_id)->where('status', 1)->whereNull('deleted_at')->first();
                $listingdata->product_id = $listingdata->id;
                $listingdata->description = NULL;
                $avgProductRating = DB::table('reviews')->where('product_id', $listingdata->id)->avg('rating');
                $listingdata->rating = $avgProductRating ? number_format($avgProductRating, 1) : number_format(0, 1);
                $listingPhotos = DB::table('listingphotos')->where('listing_id', $value->listing_id)->whereNull('deleted_at')->get();

                $cartQuery = DB::table('carts')->where('user_id', auth('api')->user()->id)->where('product_id', $listingdata->id)->whereNull('deleted_at')->first();
                $listingdata->cart_id = $cartQuery->id ?? 0;

                $image = array();
                if(!empty($listingPhotos)) {
                    foreach ($listingPhotos as $pkey => $pvalue) {
                        if($pvalue->image_1) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_1;
                        }
                        if($pvalue->image_2) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_2;   
                        }
                        if($pvalue->image_3) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_3;
                        }
                        if($pvalue->image_4) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_4;
                        }
                        if($pvalue->image_5) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_5;
                        }
                    }
                }
                $listingdata->images = $image;
                $listingdata->offset = $nextOffset;

                $sellerInfo = DB::table('listings')->select('listings.*', 'users.name', 'users.lastname', 'users.image')->leftJoin('users', 'listings.user_id', '=', 'users.id')->where('listings.id', $value->listing_id)->whereNull('listings.deleted_at')->get();
                $sellerData = array();
                if($sellerInfo) {
                    foreach($sellerInfo as $sValue) {
                        $seller['sellerName'] = $sValue->name;
                        $seller['image'] = ($sValue->image) ? url('/').'/uploads/profile/'.$sValue->image : '';
                        $seller['distance'] = number_format(0, 1);
                        $seller['rating'] = number_format(0, 1);

                        $sellerData = $seller;
                    }
                }
                $listingdata->seller = $sellerData;

                $data[] = $listingdata;
            }

            return response()->json([
                'message' => 'Listed successfully',
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
     * Add to cart
     **/
    public function addToCart(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->product_id && !$request->quantity) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }
            
            $req = Validator::make($request->all(), [
                'product_id' => 'required',
                'quantity' => 'required',
            ]);
            
            $listingdata = DB::table('listingdatas')->where('id', $request->product_id)->where('status', 1)->whereNull('deleted_at')->first();
            if(!$listingdata) {
                return response()->json(['message' => 'Product no more available to sell', 'data' => array()], 404);
            }

            if($req->fails()){
                return response()->json(['message' => 'Validation failed', 'data' => array()], 400);
            }
            $sellPrice = $listingdata->selling_price;
            
            $listings = DB::table('listings')->where('id', $listingdata->listing_id)->first();

            if($request->cart_id) {
                DB::table('carts')->where('id', $request->cart_id)->update(['quantity' => $request->quantity, 'price' => ($sellPrice * $request->quantity), 'delivery_type' => $request->delivery_type, 'product_attribute' => $request->product_attribute]);
                $cart = $request->cart_id;
                $message = 'Cart updated successfully';
            } else {
                $query = DB::table('carts')->where('user_id', auth('api')->user()->id)->where('product_id', $request->product_id)->where('status', 1)->whereNull('deleted_at');
                if($request->product_attribute) {
                    $query->where('product_attribute', $request->product_attribute);
                }
                $chkSameProductInCart = $query->first();
                if($chkSameProductInCart) {
                    return response()->json([
                        'message' => 'Product already in cart',
                        'data' => array('cart_id' => $chkSameProductInCart->id)
                    ], 400);                    
                }
                
                $cart = DB::table('carts')->insertGetId(array_merge(
                        $req->validated(),
                        ['product_attribute' => $request->product_attribute, 'price' => ($sellPrice * $request->quantity), 'delivery_type' => $request->delivery_type, 'user_id' => auth('api')->user()->id, 'seller_id' => $listings->user_id ]
                    ));
                $message = 'Product added to cart successfully';

                //Product is directly passed to checkout page
                if($request->isCheckout) {
                    $cartList = DB::table('carts')->where('user_id', auth('api')->user()->id)->where('status', 1)->whereNull('deleted_at')->get();
                    if(count($cartList) == 0) {
                        return response()->json(['message' => 'Empty records', 'data' => array()], 404);
                    }
                    $data = array();
                    $deliveryCost = 0;
                    foreach ($cartList as $key => $value) {
                        $listingdata = DB::table('listingdatas')->where('id', $value->product_id)->where('status', 1)->whereNull('deleted_at')->first();
                        
                        $listingPhotos = DB::table('listingphotos')->where('listing_id', $listingdata->listing_id)->whereNull('deleted_at')->get();

                        $categoryDetail = DB::table('listings')->select('category_id', DB::raw('(SELECT categories.name FROM `categories` where id = listings.category_id) as category_name'))->where('id', $listingdata->listing_id)->whereNull('deleted_at')->first();

                        $image = array();
                        if(!empty($listingPhotos)) {
                            foreach ($listingPhotos as $pkey => $pvalue) {
                                if($pvalue->image_1) {
                                    $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_1;
                                }
                                if($pvalue->image_2) {
                                    $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_2;   
                                }
                                if($pvalue->image_3) {
                                    $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_3;
                                }
                                if($pvalue->image_4) {
                                    $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_4;
                                }
                                if($pvalue->image_5) {
                                    $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_5;
                                }
                            }
                        }
                        $listingdata->images = $image;
                        //$value->offset = $nextOffset;
                        
                        $sellerInfo = DB::table('listings')->select('listings.*', 'users.name', 'users.lastname', 'users.image')->leftJoin('users', 'listings.user_id', '=', 'users.id')->where('listings.id', $listingdata->listing_id)->whereNull('listings.deleted_at')->get();
                        $sellerData = array();
                        if($sellerInfo) {
                            foreach($sellerInfo as $sValue) {
                                $seller['sellerName'] = $sValue->name;
                                $seller['image'] = ($sValue->image) ? url('/').'/uploads/profile/'.$sValue->image : '';
                                $seller['distance'] = number_format(0, 1);
                                $seller['rating'] = number_format(0, 1);

                                $sellerData = $seller;
                            }
                        }
                        $listingdata->seller = $sellerData;
                        $listingdata->description = NULL;
                        $listingdata->category_id = $categoryDetail->category_id ?? '';
                        $listingdata->category_name = $categoryDetail->category_name ?? '';
                        $avgProductRating = DB::table('reviews')->where('product_id', $value->product_id)->avg('rating');
                        $listingdata->rating = $avgProductRating ? number_format($avgProductRating, 1) : number_format(0, 1);

                        $value->product_details = $listingdata;
                        $deliveryCost = $deliveryCost + $listingdata->local_delivery_charge;
                        $data[] = $value;
                    }

                    $cartData['order_data'] = $data;
                    $cartTotal = DB::table('carts')->select(DB::raw('SUM(price) as total'))->where('user_id', auth('api')->user()->id)->where('status', 1)->whereNull('deleted_at')->first();
                    $cartData['total_amount'] = number_format($cartTotal->total, 2);
                    $cartData['discount'] = number_format(0, 2);
                    $cartData['delivery_charge'] = number_format($deliveryCost, 2);
                    $cartData['gst'] = number_format(($cartTotal->total * 18)/100, 2) ?? number_format(0,2);
                    $cartData['grand_total'] = number_format( ($cartTotal->total + (($cartTotal->total * 18)/100) + $deliveryCost), 2) ?? number_format(0, 2);

                    return response()->json([
                        'message' => 'Listed successfully',
                        'data' => $cartData
                    ], 200);    
                }
                //End product is directly passed to checkout page
            }

            return response()->json([
                'message' => $message,
                'data' => array('cart_id' => $cart)
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }   
    }

    /** 
     * Cart List
     **/
    public function cartProducts(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            $query = DB::table('carts')->where('user_id', auth('api')->user()->id)->where('status', 1)->whereNull('deleted_at');

            $offset = $request->offset;
            if(!$offset) {
                $offset = 0;
            }
            $limit = $request->limit ? $request->limit : 10;
            
            $cartList = $query->offset($offset)->limit($limit)->get();
            if(count($cartList) == 0) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);
            }
            $nextOffset = count($cartList) + $offset;
            $data = array();
            foreach ($cartList as $key => $value) {
                $listingdata = DB::table('listingdatas')->where('id', $value->product_id)->where('status', 1)->whereNull('deleted_at')->first();
                $avgProductRating = DB::table('reviews')->where('product_id', $value->product_id)->avg('rating');
                $listingdata->rating = $avgProductRating ? number_format($avgProductRating, 1) : number_format(0, 1);
                
                $listingPhotos = DB::table('listingphotos')->where('listing_id', $listingdata->listing_id)->whereNull('deleted_at')->get();

                $image = array();
                if(!empty($listingPhotos)) {
                    foreach ($listingPhotos as $pkey => $pvalue) {
                        if($pvalue->image_1) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_1;
                        }
                        if($pvalue->image_2) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_2;   
                        }
                        if($pvalue->image_3) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_3;
                        }
                        if($pvalue->image_4) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_4;
                        }
                        if($pvalue->image_5) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_5;
                        }
                    }
                }
                $listingdata->images = $image;
                $value->offset = $nextOffset;
                
                $sellerInfo = DB::table('listings')->select('listings.*', 'users.name', 'users.lastname', 'users.image')->leftJoin('users', 'listings.user_id', '=', 'users.id')->where('listings.id', $listingdata->listing_id)->whereNull('listings.deleted_at')->get();
                $sellerData = array();
                if($sellerInfo) {
                    foreach($sellerInfo as $sValue) {
                        $seller['sellerName'] = $sValue->name;
                        $seller['image'] = ($sValue->image) ? url('/').'/uploads/profile/'.$sValue->image : '';
                        $seller['distance'] = number_format(0, 1);
                        $seller['rating'] = number_format(0, 1);

                        $sellerData = $seller;
                    }
                }
                $listingdata->seller = $sellerData;
                $listingdata->description = NULL;

                $value->product_details = $listingdata;
                $data[] = $value;
            }

            return response()->json([
                'message' => 'Listed successfully',
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
     * Remove from cart
     * */
    public function removeProductFromCart(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->cart_id) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $chkCartData = DB::table('carts')->where('id', $request->cart_id)->where('user_id', auth('api')->user()->id)->whereNull('deleted_at')->first();
            if(!$chkCartData) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);
            }

            if($chkCartData) {
                DB::table('carts')->where('id', $request->cart_id)->update(['deleted_at' => date('Y-m-d H:i:s') ]);
                $message = 'Product removed from cart successfully';
            }

            return response()->json([
                'message' => $message,
                'data' => array('cart_id' => $request->cart_id)
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }   
    }

    /** 
     * Checkout List
     **/
    public function checkOutList(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if($request->cart_update) {
                $cartUpdate = json_decode($request->cart_update); 
                if(count($cartUpdate) > 0) {
                    foreach ($cartUpdate as $key => $value) {
                        $cart_chk = DB::table('carts')->where('id', $value->cart_id)->whereNull('deleted_at')->first();
                        $listingdata1 = DB::table('listingdatas')->where('id', $cart_chk->product_id)->where('status', 1)->whereNull('deleted_at')->first();
                        //product available than update cart
                        if($listingdata1) {
                            $sellPrice = $listingdata1->selling_price;
                            DB::table('carts')->where(['id' => $value->cart_id, 'user_id' => auth('api')->user()->id ])->update(['quantity' => $value->quantity, 'price' => ($sellPrice * $value->quantity) ]);
                        }
                        
                    }
                }
            }

            $cartList = DB::table('carts')->where('user_id', auth('api')->user()->id)->where('status', 1)->whereNull('deleted_at')->get();
            if(count($cartList) == 0) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);
            }
            
            $data = array();
            $deliveryCost = 0;
            foreach ($cartList as $key => $value) {
                $listingdata = DB::table('listingdatas')->where('id', $value->product_id)->where('status', 1)->whereNull('deleted_at')->first();
                $listingPhotos = DB::table('listingphotos')->where('listing_id', $listingdata->listing_id)->whereNull('deleted_at')->get();
                $categoryDetail = DB::table('listings')->select('category_id', DB::raw('(SELECT categories.name FROM `categories` where id = listings.category_id) as category_name'))->where('id', $listingdata->listing_id)->whereNull('deleted_at')->first();

                $image = array();
                if(!empty($listingPhotos)) {
                    foreach ($listingPhotos as $pkey => $pvalue) {
                        if($pvalue->image_1) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_1;
                        }
                        if($pvalue->image_2) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_2;   
                        }
                        if($pvalue->image_3) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_3;
                        }
                        if($pvalue->image_4) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_4;
                        }
                        if($pvalue->image_5) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_5;
                        }
                    }
                }
                $listingdata->images = $image;
                
                $sellerInfo = DB::table('listings')->select('listings.*', 'users.name', 'users.lastname', 'users.image')->leftJoin('users', 'listings.user_id', '=', 'users.id')->where('listings.id', $listingdata->listing_id)->whereNull('listings.deleted_at')->get();
                $sellerData = array();
                if($sellerInfo) {
                    foreach($sellerInfo as $sValue) {
                        $seller['sellerName'] = $sValue->name;
                        $seller['image'] = ($sValue->image) ? url('/').'/uploads/profile/'.$sValue->image : '';
                        $seller['distance'] = number_format(0, 1);
                        $seller['rating'] = number_format(0, 1);

                        $sellerData = $seller;
                    }
                }
                $listingdata->seller = $sellerData;
                $listingdata->description = NULL;
                $listingdata->category_id = $categoryDetail->category_id ?? '';
                $listingdata->category_name = $categoryDetail->category_name ?? '';
                $avgProductRating = DB::table('reviews')->where('product_id', $value->product_id)->avg('rating');
                $listingdata->rating = $avgProductRating ? number_format($avgProductRating, 1) : number_format(0, 1);

                $value->product_details = $listingdata;
                $deliveryCost = $deliveryCost + $listingdata->local_delivery_charge;
                $data[] = $value;
            }
            
            $cart['order_data'] = $data;
            $cartTotal = DB::table('carts')->select(DB::raw('SUM(price) as total'))->where('user_id', auth('api')->user()->id)->where('status', 1)->whereNull('deleted_at')->first();
            $cart['total_amount'] = number_format($cartTotal->total, 2);
            $cart['discount'] = number_format(0, 2);
            $cart['delivery_charge'] = number_format($deliveryCost, 2);
            $cart['gst'] = number_format(($cartTotal->total * 18)/100, 2) ?? number_format(0,2);
            $cart['grand_total'] = number_format( ($cartTotal->total + (($cartTotal->total * 18)/100) + $deliveryCost), 2) ?? number_format(0, 2);
            
            return response()->json([
                'message' => 'Listed successfully',
                'data' => $cart
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }

    /**
     * Ordering Process
     * */
    public function orderingProcess(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->cart_id && !$request->address_id && !$request->payment_mode && !$request->total_price) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $cartIDs = explode(',', $request->cart_id);

            $cartTotal = DB::table('carts')->select(DB::raw('SUM(price) as total'))->whereIn('id', explode(',', $request->cart_id))->whereNull('deleted_at')->first();
            $cartDeliveryCharge = DB::table('listingdatas')->select(DB::raw('SUM(local_delivery_charge) as deliveryCharge'))
                                                            ->whereIn('id', function($query) use ($cartIDs) {
                                                                $query->select('product_id')->from('carts')->whereIn('id', $cartIDs)->whereNull('deleted_at');
                                                            })->whereNull('deleted_at')->first();

            $total_amount = $cartTotal->total;
            $discount = number_format(0, 2);
            $delivery_charge = number_format($cartDeliveryCharge->deliveryCharge, 2);
            $gst = number_format(($cartTotal->total * 18)/100, 2) ?? number_format(0,2);
            $grand_total = ($cartTotal->total + (($cartTotal->total * 18)/100) + $cartDeliveryCharge->deliveryCharge) ?? number_format(0, 2);
            $order_id = strtotime("now");
            //echo 'total amount: '.$total_amount;
            //echo ' grand total: '.$grand_total;die;
            DB::table('orders')->insert(['order_id' => $order_id, 'user_id' => auth('api')->user()->id, 'cart_id' => $request->cart_id, 'order_status' => 2, 'address_id' => $request->address_id, 'payment_mode' => 1, 'total_amount' => $total_amount, 'discount'=> $discount, 'delivery_charge' => $delivery_charge, 'gst' => $gst, 'grand_total' => $grand_total ]);
            $message = 'Order Placed successfully';
            
            if(count($cartIDs) > 0) {
                foreach ($cartIDs as $key => $value) {
                    DB::table('carts')->where('id', $value)->update(['status'=>2]);
                }    
            }
            
            $orderList = DB::table('orders')->where('order_id', $order_id)->whereNull('deleted_at')->get();
            if(empty($orderList)) {
                return response()->json(['message' => 'No orders found!', 'data' => array()], 404);   
            }
            $cart = array();
            foreach ($orderList as $okey => $ovalue) {
                $ovalue->cart_ids = $ovalue->cart_id;
                $cartList = DB::table('carts')->whereIn('id', explode(',', $ovalue->cart_id))->whereNull('deleted_at')->get();
                if(count($cartList) == 0) {
                    continue;
                }
                $data = array();
                foreach ($cartList as $key => $value) {
                    $listingdata = DB::table('listingdatas')->where('id', $value->product_id)->where('status', 1)->whereNull('deleted_at')->first();
                    $listingPhotos = DB::table('listingphotos')->where('listing_id', $listingdata->listing_id)->whereNull('deleted_at')->get();
                    $categoryDetail = DB::table('listings')->select('category_id', DB::raw('(SELECT categories.name FROM `categories` where id = listings.category_id) as category_name'))->where('id', $listingdata->listing_id)->whereNull('deleted_at')->first();

                    $image = array();
                    if(!empty($listingPhotos)) {
                        foreach ($listingPhotos as $pkey => $pvalue) {
                            if($pvalue->image_1) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_1;
                            }
                            if($pvalue->image_2) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_2;   
                            }
                            if($pvalue->image_3) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_3;
                            }
                            if($pvalue->image_4) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_4;
                            }
                            if($pvalue->image_5) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_5;
                            }
                        }
                    }
                    $listingdata->images = $image;
                    
                    $sellerInfo = DB::table('listings')->select('listings.*', 'users.name', 'users.lastname', 'users.image')->leftJoin('users', 'listings.user_id', '=', 'users.id')->where('listings.id', $listingdata->listing_id)->whereNull('listings.deleted_at')->get();
                    $sellerData = array();
                    if($sellerInfo) {
                        foreach($sellerInfo as $sValue) {
                            $seller['sellerName'] = $sValue->name;
                            $seller['image'] = ($sValue->image) ? url('/').'/uploads/profile/'.$sValue->image : '';
                            $seller['distance'] = number_format(0, 1);
                            $seller['rating'] = number_format(0, 1);

                            $sellerData = $seller;
                        }
                    }
                    $listingdata->seller = $sellerData;
                    $listingdata->description = NULL;
                    $listingdata->category_id = $categoryDetail->category_id ?? '';
                    $listingdata->category_name = $categoryDetail->category_name ?? '';
                    $avgProductRating = DB::table('reviews')->where('product_id', $value->product_id)->avg('rating');
                    $listingdata->rating = $avgProductRating ? number_format($avgProductRating, 1) : number_format(0, 1);

                    $value->product_details = $listingdata;
                    $data[] = $value;
                }
                $ovalue->order_data = $data;
                
                $cartTotal = DB::table('carts')->select(DB::raw('SUM(price) as total'))->whereIn('id', explode(',', $ovalue->cart_id))->whereNull('deleted_at')->first();

                $ovalue->payment_mode = $ovalue->payment_mode;
                $ovalue->order_status_id = $ovalue->order_status;
                $ovalue->order_status = $ovalue->order_status == 1 ? 'Processing' : ($ovalue->order_status == 2 ? 'Pending' : ($ovalue->order_status == 3 ? 'Paid' : 'Cancelled'));
                $UserAddress = DB::table('user_address')->where('id', $ovalue->address_id)->whereNull('deleted_at')->first();
                $ovalue->address = $UserAddress;
                unset($ovalue->cart_id);
                $cart[] = $ovalue;
            }

            return response()->json([
                'message' => $message,
                'data' => $cart
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }

    /**
     * Order History
     * */
    public function orderHistory(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            $query = DB::table('orders')->where('user_id', auth('api')->user()->id)->whereNull('deleted_at');
            if($request->order_id) {
                $query = $query->where('order_id',$request->order_id);
            } else {
                $query = $query->where('order_status', '>=', 2);
            }
            
            $offset = $request->offset;
            if(!$offset) {
                $offset = 0;
            }
            $limit = $request->limit ? $request->limit : 10; 
            $orderList = $query->offset($offset)->limit($limit)->get();
            if(empty($orderList)) {
                return response()->json(['message' => 'No orders found!', 'data' => array()], 404);   
            }
            $nextOffset = count($orderList) + $offset;
            $cart = array();
            foreach ($orderList as $okey => $ovalue) {
                $cartList = DB::table('carts')->whereIn('id', explode(',', $ovalue->cart_id))->whereNull('deleted_at')->get();
                if(count($cartList) == 0) {
                    continue;
                }
                $data = array();
                foreach ($cartList as $key => $value) {
                    $listingdata = DB::table('listingdatas')->where('id', $value->product_id)->where('status', 1)->whereNull('deleted_at')->first();
                    $listingPhotos = DB::table('listingphotos')->where('listing_id', $listingdata->listing_id)->whereNull('deleted_at')->get();
                    $categoryDetail = DB::table('listings')->select('category_id', DB::raw('(SELECT categories.name FROM `categories` where id = listings.category_id) as category_name'))->where('id', $listingdata->listing_id)->whereNull('deleted_at')->first();

                    $image = array();
                    if(!empty($listingPhotos)) {
                        foreach ($listingPhotos as $pkey => $pvalue) {
                            if($pvalue->image_1) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_1;
                            }
                            if($pvalue->image_2) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_2;   
                            }
                            if($pvalue->image_3) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_3;
                            }
                            if($pvalue->image_4) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_4;
                            }
                            if($pvalue->image_5) {
                                $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_5;
                            }
                        }
                    }
                    $listingdata->images = $image;
                    
                    $sellerInfo = DB::table('listings')->select('listings.*', 'users.name', 'users.lastname', 'users.image')->leftJoin('users', 'listings.user_id', '=', 'users.id')->where('listings.id', $listingdata->listing_id)->whereNull('listings.deleted_at')->get();
                    $sellerData = array();
                    if($sellerInfo) {
                        foreach($sellerInfo as $sValue) {
                            $seller['sellerName'] = $sValue->name;
                            $seller['image'] = ($sValue->image) ? url('/').'/uploads/profile/'.$sValue->image : '';
                            $seller['distance'] = number_format(0, 1);
                            $seller['rating'] = number_format(0, 1);

                            $sellerData = $seller;
                        }
                    }
                    $listingdata->seller = $sellerData;
                    $listingdata->description = NULL;
                    $listingdata->category_id = $categoryDetail->category_id ?? '';
                    $listingdata->category_name = $categoryDetail->category_name ?? '';
                    $avgProductRating = DB::table('reviews')->where('product_id', $value->product_id)->avg('rating');
                    $listingdata->rating = $avgProductRating ? number_format($avgProductRating, 1) : number_format(0, 1);

                    $value->product_details = $listingdata;
                    $data[] = $value;
                }
                $ovalue->order_data = $data;
                $cartTotal = DB::table('carts')->select(DB::raw('SUM(price) as total'))->whereIn('id', explode(',', $ovalue->cart_id))->whereNull('deleted_at')->first();

                // $ovalue->total_amount = number_format($cartTotal->total, 2);
                // $ovalue->discount = number_format(0, 2);
                // $ovalue->delivery_charge = number_format(0, 2);
                // $ovalue->gst = number_format(($cartTotal->total * 18)/100, 2) ?? number_format(0,2);
                // $ovalue->grand_total = number_format( ($cartTotal->total + (($cartTotal->total * 18)/100)), 2) ?? number_format(0, 2);
                $ovalue->payment_mode = $ovalue->payment_mode;
                $ovalue->order_status_id = $ovalue->order_status;
                $ovalue->order_status = $ovalue->order_status == 1 ? 'Processing' : ($ovalue->order_status == 2 ? 'Pending' : ($ovalue->order_status == 3 ? 'Paid' : 'Cancelled'));
                $UserAddress = DB::table('user_address')->where('id', $ovalue->address_id)->whereNull('deleted_at')->first();
                $ovalue->address = $UserAddress;
                $ovalue->offset = $nextOffset;
                $cart[] = $ovalue;
            }
            
            if(empty($cart)) {
                return response()->json([
                    'message' => 'No Orders',
                    'data' => $cart
                ], 404);        
            }

            return response()->json([
                'message' => 'Order listed successfully',
                'data' => $cart
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }
    
    /**
     * Cancel Order
     * */
    public function orderCancel(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->order_id) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $order = DB::table('orders')->where('order_id', $request->order_id)->whereNull('deleted_at')->first();
            if(!$order) {
                return response()->json(['message' => 'Order not found', 'data' => array()], 404);
            }
            
            if($order->order_status == 3) {
                return response()->json(['message' => 'Order cannot be cancelled', 'data' => array()], 400);
            }

            DB::table('orders')->where('order_id', $request->order_id)->update(['order_status'=>4]);
            DB::table('carts')->whereIn('id', explode(',', $order->cart_id))->update(['status'=>5]);

            return response()->json([
                'message' => 'Order cancelled successfully',
                'data' => array(['order_id'=>$request->order_id])
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }
    
    /**
     * Review Order
     * Method: POST
     * parameters: order_id,review,rating(decimal)
     * */
    public function orderReview(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            if(!$request->order_id || (!$request->rating && !$request->review)) {
                return response()->json(['message' => 'Incomplete Request', 'data' => array()], 411);
            }

            $review = DB::table('reviews')->where('order_id', $request->order_id)->where('product_id', $request->product_id)->where('user_id', auth('api')->user()->id)->whereNull('deleted_at')->first();
            if($review) {
                return response()->json(['message' => 'You have already reviewed this order', 'data' => array(['order_id'=>$review->order_id, 'rating'=>$review->rating, 'review'=>$review->review])], 200);
            }
            
            DB::table('reviews')->insert(['order_id'=>$request->order_id, 'product_id'=>$request->product_id, 'user_id'=>auth('api')->user()->id, 'rating'=>$request->rating, 'review'=>$request->review]);

            return response()->json([
                'message' => 'Review inserted successfully',
                'data' => array(['order_id'=>$request->order_id, 'rating'=>$request->rating, 'review'=>$request->review])
            ], 200);    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);   
        }
    }


    /**
    * Product Filter List
    * Slug value
    * 1=Best Selling, 2=Trending Now, 3=New & Noteworthy, 4=Accesories, 5=Recently viewed, 6=Shop now, 7=Quick buy
    */
    public function productFilterListing(Request $request) {
        try {
            $slug = $request->slug;
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }
            $categoryId = $request->category_id;
            $query = DB::table('listings')->where('status', 3)->whereNull('deleted_at');
            $offset = $request->offset;
            if(!$offset) {
                $offset = 0;
            }
            $limit = $request->limit ? $request->limit : 10; 
            $categoryList = $query->inRandomOrder()->offset($offset)->limit($limit)->get();
            if(empty($categoryList)) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);   
            }
            $nextOffset = count($categoryList) + $offset;
            $data = array();
            foreach ($categoryList as $key => $value) {
                $userData = User::where('id', $value->user_id)->first();
                $categoryData = Category::where('id', $value->category_id)->first();
                $brandData = Brand::where('brand_id', $value->brand_id)->first();
                $listingdata = DB::table('listingdatas')->where('listing_id', $value->id)->where('status', 1)->whereNull('deleted_at')->first();
                $listingdata->product_id = $listingdata->id;
                $listingdata->description = NULL;
                $listingdata->category_name = $categoryData->name;
                $listingdata->hastags = $value->hastags;
                $listingdata->brand_name = $brandData->brand_name;
                $listingPhotos = DB::table('listingphotos')->where('listing_id', $value->id)->whereNull('deleted_at')->get();
                
                $chkWhistlist = DB::table('whishlist')->where('user_id', auth('api')->user()->id)->where('listing_id',$value->id)->whereNull('deleted_at')->first();
                $listingdata->isFavorite = $chkWhistlist ? true : false;
                $avgProductRating = DB::table('reviews')->where('product_id', $listingdata->id)->avg('rating');
                $listingdata->rating = $avgProductRating ? number_format($avgProductRating, 1) : number_format(0, 1);
                $listingdata->actualPrice = number_format($listingdata->mrp, 2);
                $listingdata->discount = number_format(0, 2);

                $cartQuery = DB::table('carts')->where('user_id', auth('api')->user()->id)->where('product_id', $listingdata->id)->whereNull('deleted_at')->first();
                $listingdata->cart_id = $cartQuery->id ?? 0;

                $image = array();
                if(!empty($listingPhotos)) {
                    foreach ($listingPhotos as $pkey => $pvalue) {
                        if($pvalue->image_1) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_1;
                        }
                        if($pvalue->image_2) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_2;   
                        }
                        if($pvalue->image_3) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_3;
                        }
                        if($pvalue->image_4) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_4;
                        }
                        if($pvalue->image_5) {
                            $image[] = url('/').'/uploads/listings/'.$pvalue->listing_id.'/'.$pvalue->image_5;
                        }
                    }
                }
                $listingdata->images = $image;
                $listingdata->offset = $nextOffset;

                $sellerInfo = User::where('id', $value->user_id)->get();
                $sellerData = array();
                if($sellerInfo) {
                    foreach($sellerInfo as $sValue) {
                        $seller['sellerName'] = $sValue->name;
                        $seller['image'] = ($sValue->image) ? url('/').'/uploads/profile/'.$sValue->image : '';
                        $seller['distance'] = number_format(0, 1);
                        $seller['rating'] = number_format(0, 1);

                        $sellerData = $seller;
                    }
                }
                $listingdata->seller = $sellerData;

                $data[] = $listingdata;
            }

            if(empty($data)) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);   
            }
            
            return response()->json([
                'message' => 'Product listed successfully',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);
        }
    }
}
