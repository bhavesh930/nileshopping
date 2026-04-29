<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Hash;

use App\Models\Brand;

use App\Models\Category;

use App\Models\BrandRequest;

use DB;

use Auth;



class BrandController extends Controller

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

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        //$brand = Brand::all();

        //return view('seller.dashboard', ['parent_category'=>$category]);

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        //return view('seller.register');   

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     * 'required|regex:/(01)[0-9]{9}/' 

     * 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10'

     */

    public function store(Request $request)

    {

        $input = Request()->all();

        
        $brand = Brand::where('seller_id',$request->seller_id)->where('brand_name',$request->brand_name)->first();
        if(!$brand) {
            $brand = Brand::create($input);    
        }
        //$brand = Brand::create($input);
        

        $uniqueid = strtotime(date('Y-m-d h:i:s'));

        $categoryData = Category::where('slug', $request->vertical)->first();

        $brandData = Brand::where('brand_name', $request->brand_name)->where('seller_id',$request->seller_id)->first();

        DB::table('listings')->insert( ['user_id'=>$request->seller_id, 'category_id' => $categoryData->id, 'brand_id' => $brandData->brand_id, 'unique_id' => $uniqueid] );

        if($brand) {

            return $uniqueid;

        } else {

            return false;

        }

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        //

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy(Question $question)

    {

        

    }



    /**
    * Checking brand name
    *
    * @param string $id
    * @return \Illuminate\Http\Response
    */

    public function check(Request $request, $id)
    {
        //->where('seller_id', $request->user_id)
        $brandData = Brand::where('brand_name', $id)->where('seller_id', $request->user_id)->first();
        if($brandData) {
            return $brandData;    
        } else {
            $brandData = Brand::where('brand_name', $id)->first();
            return $brandData;
        }
    }



    public function brandList(Request $request)

    {

        $brand = $request->brand;

        $user_id = Auth::user()->id;

        $user_role = Auth::user()->menuroles;

        $arguments = array('user_id'=> $user_id, 'user_role' => $user_role);

        $brandList = BrandRequest::brandRequestList($arguments);

        return view('brand.list', ["brandList"=>$brandList,'user_role' => $user_role]);

    }



    public function brandStatusChange(Request $request, $id)

    {

        $arguments = array('id'=> $id, 'status' => $request->status);

        $status = BrandRequest::brandStatusChange($arguments);

        if($request->status == 1) {

            $brandRequestData = BrandRequest::where('id', $id)->first();

            Brand::insert(['seller_id' => $brandRequestData->user_id, "brand_name" => $brandRequestData->brand_name]);

        }

    }



    public function brandApproval(Request $request)

    {

        $brand = $request->brand;

        // $user_id = Auth::user()->id;

        // $arguments = array('user_id'=> $user_id, 'type' => 'mylist');

        // $listings = Seller::listing($arguments);

        return view('seller.brandApproval', ["brand"=>$brand]);

    }



    public function brandApprovalStore(Request $request)

    {

        // if( $listingPhotos && file_exists(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_1)) ) {

        //     unlink(url('/').'/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_1);

        // }

        $brandLogo = '';

        if($request->file('brand_logo')){

            $image1 = $this->uploadImage($request->brand_logo, '/uploads/brands');

            $brandLogo = $image1;

        }

        $mrpLogo = '';

        if($request->file('mrp_tag')){

            $image2 = $this->uploadImage($request->mrp_tag, '/uploads/brands');

            $mrpLogo = $image2;

        }

        $tradeLogo = '';

        if($request->file('trademark_doc')){

            $image3 = $this->uploadImage($request->trademark_doc, '/uploads/brands');

            $tradeLogo = $image3;

        }



        $brandRequest = new BrandRequest;

        $brandRequest->user_id = Auth::user()->id;

        $brandRequest->brand_name = $request->brand_name;

        $brandRequest->brand_logo = $brandLogo;

        $brandRequest->offline_market = $request->offline_market;

        $brandRequest->website_link = $request->website;

        $brandRequest->sell_product_brand = $request->sell_product;

        $brandRequest->mrp_tag = $mrpLogo;

        $brandRequest->brand_owner = $request->brand_owner;

        $brandRequest->trademark_doc = $tradeLogo;

        $brandRequest->document_type = $request->document_type;

        

        //$brandRequest->image = request()->file('image')->store('public/images');

        $brandRequest->save();



        return redirect('/brands');

    }



}

