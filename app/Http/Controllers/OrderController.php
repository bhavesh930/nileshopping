<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Listing;
use App\Models\Seller;
use DB;
use Auth;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the order.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::user()->id;
        $user_role = Auth::user()->menuroles;
        if(in_array('admin', explode(',', $user_role) )) {
            $order = DB::table('orders')->whereNull('deleted_at')->orderBy('id', 'desc')->get();    
        }
        
        if(in_array('seller', explode(',', $user_role) )) {
            //SELECT * FROM `orders` as ord WHERE FIND_IN_SET('31', (SELECT GROUP_CONCAT(carts.seller_id) FROM `carts` where CONCAT(',', ord.cart_id, ',') REGEXP CONCAT(',(', REPLACE(carts.id, ',', '|'), '),')) ) ORDER BY id desc    
            $order = DB::table('orders as ord')->whereRaw('FIND_IN_SET("'.$user_id.'", (SELECT GROUP_CONCAT(carts.seller_id) FROM `carts` where CONCAT(",", ord.cart_id, ",") REGEXP CONCAT(",(", REPLACE(carts.id, ",", "|"), "),"))  )')->orderBy('id', 'desc')->get();    
        }
        
        return view('dashboard.order.list', ['orders'=>$order]);
    }

    /**
     * Display the specified order detail.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_id = Auth::user()->id;
        $user_role = Auth::user()->menuroles;
        $order = DB::table('orders')->where('order_id', $id)->first();
        $cartQuery = DB::table('carts')->whereIn('id', explode(',', $order->cart_id));
        if(in_array('seller', explode(',', $user_role) )) {
            $cartQuery = $cartQuery->where('seller_id', $user_id);
        }
        $cartList = $cartQuery->whereNull('deleted_at')->get();
        $orderDetail = array();
        if(count($cartList) > 0) {
            foreach ($cartList as $key => $value) {
                $listingdata = DB::table('listingdatas')->where('id', $value->product_id)->where('status', 1)->whereNull('deleted_at')->first();
                $categoryDetail = DB::table('listings')->select('unique_id', 'category_id', DB::raw('(SELECT categories.name FROM `categories` where id = listings.category_id) as category_name'))->where('id', $listingdata->listing_id)->whereNull('deleted_at')->first();
                
                $value->product_id = $categoryDetail->unique_id;
                $value->product_name = $listingdata->product_name;
                $value->category = $categoryDetail->category_name;
                $value->sku = $listingdata->sku;
                $value->mrp = $listingdata->mrp;
                $value->selling_price = $listingdata->selling_price;
                $value->hsn = $listingdata->hsn;
                $value->modal_number = $listingdata->modal_number;
                
                $orderDetail[] = $value;
            }
        }
        return view('dashboard.order.detail', ['order_id'=>$id, 'order_details'=>$orderDetail]);
    }
    
    public function orderStatusUpdate(Request $request) {
        DB::table('carts')->where('id', $request->cart_id)->update(array("status"=>$request->status));
        
        return back();
    }

    /**
     * Show the form for editing the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

}
