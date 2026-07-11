<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Question;
use App\Models\Seller;
use App\Models\Brand;
use DB;
use Auth;
use Spatie\Permission\Models\Role;  // ← Add this line
use Spatie\Permission\PermissionRegistrar;  // ← Add this for the cache clearing

class SellerController extends Controller
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
        $category = Category::with('parent')->whereNull('parent_id')->get();
        //print_r($category);die();
        return view('seller.dashboard', ['parent_category'=>$category]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('seller.register');   
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
        $validatedData = $this->validate($request, [
            'name'          => 'required|min:3|max:255|string',
            'phone'         => 'required|min:3|max:255|unique:sellers|string',         //|unique:sellers
            'email'         => 'required|email|max:255|unique:users|string',         //unique:sellers|
            'password'      => 'required|min:3|max:20|string|confirmed',
            'storeEmail'    => 'required|email|max:255|string',
            'storePhone'    => 'required|digits:10|numeric',
            'country'       => 'required|min:3|max:255|string',
            'state'         => 'required|min:3|max:255|string',
            'city'          => 'required|min:3|max:255|string',
            'address'       => 'required|min:3|string',
            'pincode'       => 'required|min:5|max:8|string',
            'employeeCode'   => 'sometimes|nullable|string'
        ]);

        if ($validatedData->fails()) {
            return redirect('seller/account')
                        ->withErrors($validatedData)
                        ->withInput();
        }

        $user =  User::insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => date('Y-m-d H:i:s'),
            'menuroles' => 'seller',
        ]);
        //echo $user;die();
        //$validatedData['user_id'] = $user;
        $seller = new Seller;
        $seller->user_id = $user;
        $seller->phone = $request->phone;
        $seller->storeEmail = $request->storeEmail;
        $seller->storePhone = $request->storePhone;
        $seller->country = $request->country;
        $seller->state = $request->state;
        $seller->city = $request->city;
        $seller->address = $request->address;
        $seller->pincode = $request->pincode;
        $seller->employeeCode = $request->employeeCode;
        //Seller::create($validatedData);
        
        //$seller->image = request()->file('image')->store('public/images');
        $seller->save();
        
        //return redirect('/seller');
        return redirect()->route('login')->withSuccess('Your account has been created successfully as a Seller!');
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
    * Retrive Question for particular Category
    *
    * @param int $id
    * @return \Illuminate\Http\Response
    */
    public function categoryQuestionList($id)
    {
        
    }

    public function listingBrandSelection(Request $request)
    {
        $slug = $request->vertical;
        $user_id = Auth::user()->id;
        $userBrand = Brand::where('seller_id', $user_id)->whereNull('deleted_at')->get();
        return view('seller.brandSelect', ['vertical'=>$slug, 'userBrand'=>$userBrand]);   
    }

    public function createListing(Request $request)
    {   
        $vertical = $request->vertical;
        $brand = $request->brand;
        $data = array('vertical'=>$vertical, 'brand'=>$brand);
        $categoryData = Category::where('slug', $vertical)->first();
        $listing = DB::table('listings')->where('unique_id', $request->id)->first(); 
        $data['vertical_id'] = $categoryData->id;
        $data['brand_id'] = $listing->brand_id;
        $data['listing_id'] = $listing->id;
        $data['listing_status'] = $listing->status;
        if($categoryData) {
            $data['questioList'] = Question::getCategoryQuestionData($categoryData->id);  
            $attribute_data = DB::table('attributes')->where('category_id', $categoryData->id)->where('flag', 1)->get();
            $data['attributes'] = $attribute_data ?? array();  
        }
        $listingData = DB::table('listingdatas')->where('listing_id', $listing->id)->first();
        if($listingData) {
            $data['listingData'] = $listingData;  
            $sizeData = DB::table('listing_sizechart')->where('listing_id', $listing->id)->get();
            $data['sizeData'] = $sizeData ?? array();  
        }
        
        $listingadditions = DB::table('listingadditions')->where('listing_id', $listing->id)->where('category_id', $categoryData->id)->get();
        $userFieldRequiredCnt = 0;
        if($listingadditions) {
            $data['additionalData'] = $listingadditions;

            $requiredAdditional = DB::table('questions')->leftJoin('listingadditions', 'questions.id', '=', 'listingadditions.question_id')->where('questions.category_id', $categoryData->id)->where('listingadditions.listing_id', $listing->id)->where('questions.required', 1)->whereNotNull('listingadditions.answer')->get();
            $userFieldRequiredCnt = count($requiredAdditional);
        }
        $data['additionalRequiredCount'] = $userFieldRequiredCnt;

        $totalRequiredAdditions = DB::table('questions')->where('required', 1)->where('category_id', $categoryData->id)->get();
        $data['additionalTotalRequiredCount'] = ($totalRequiredAdditions) ? count($totalRequiredAdditions) : 0;

        $listingPhotos = DB::table('listingphotos')->where('listing_id',$listing->id)->first();
        if($listingPhotos) {
            $data['listingPhotos'] = $listingPhotos;    
        }

        $data['additionalQuesView'] = Seller::additionalQuestionViewDashboard(array('listing_id'=>$listing->id, 'limit'=>6));
        return view('seller.listing.create', $data);
    }

    public function storeListingAddition(Request $request)
    {
        if(isset($request->product) && !empty($request->product)) {
            foreach ($request->product as $key => $value) {
                $category_id = $key;
                foreach ($value as $lkey => $lvalue) {
                    $question_id = $lkey;

                    DB::table('listingadditions')->updateOrInsert( ['listing_id'=>$request->listing_id, 'category_id' => $category_id, 'question_id' => $question_id], ['answer' => $lvalue] );
                }
            }    
        }
        
        $listing = DB::table('listings')->where('id', $request->listing_id)->first(); 
        $categoryData = Category::where('id', $listing->category_id)->first();
        $brandData = Brand::where('brand_id', $listing->brand_id)->first();

        return redirect()->route('createListing', ['vertical'=>$categoryData->slug, 'brand'=>$brandData->brand_name, 'id'=>$listing->unique_id]);
    }

    public function storeListingData(Request $request)
    {
        $listing = DB::table('listings')->where('id', $request->listing_id)->first();
        $listing_id = $listing->id;
        if(isset($request->product_name)){
            $dataArr['product_name'] = $request->product_name;    
        }

        if(isset($request->sku)){
            $dataArr['sku'] = $request->sku;    
        }
        
        if(isset($request->status)){
            $dataArr['status'] = $request->status;    
        }
        
        if(isset($request->mrp)){
            $dataArr['mrp'] = $request->mrp;
        }

        if(isset($request->selling_price)){
            $dataArr['selling_price'] = $request->selling_price;
        }

        if(isset($request->fullfilment)){
            $dataArr['fullfilment'] = $request->fullfilment;
        }

        if(isset($request->procurement_type)){
            $dataArr['procurement_type'] = $request->procurement_type;
        }

        if(isset($request->procurement_sla)){
            $dataArr['procurement_sla'] = $request->procurement_sla;
        }

        if(isset($request->stock)){
            $dataArr['stock'] = $request->stock;
        }

        if(isset($request->shipping_provider)){
            $dataArr['shipping_provider'] = $request->shipping_provider;
        }

        if(isset($request->local_delivery_charge)){
            $dataArr['local_delivery_charge'] = $request->local_delivery_charge;
        }

        if(isset($request->zonal_delivery_charge)){
            $dataArr['zonal_delivery_charge'] = $request->zonal_delivery_charge;
        }

        if(isset($request->national_delivery_charge)){
            $dataArr['national_delivery_charge'] = $request->national_delivery_charge;
        }

        if(isset($request->package_weight)){
            $dataArr['package_weight'] = $request->package_weight;
        }

        if(isset($request->package_length)){
            $dataArr['package_length'] = $request->package_length;
        }

        if(isset($request->package_breadth)){
            $dataArr['package_breadth'] = $request->package_breadth;
        }

        if(isset($request->package_height)){
            $dataArr['package_height'] = $request->package_height;
        }

        if(isset($request->hsn)){
            $dataArr['hsn'] = $request->hsn;
        }

        if(isset($request->luxury_cess)){
            $dataArr['luxury_cess'] = $request->luxury_cess;
        }

        if(isset($request->tax_code)){
            $dataArr['tax_code'] = $request->tax_code;
        }

        if(isset($request->country_origin)){
            $dataArr['country_origin'] = $request->country_origin;
        }

        if(isset($request->manufacturer_detail)){
            $dataArr['manufacturer_detail'] = $request->manufacturer_detail;
        }

        if(isset($request->packer_detail)){
            $dataArr['packer_detail'] = $request->packer_detail;
        }

        if(isset($request->importer_detail)){
            $dataArr['importer_detail'] = $request->importer_detail;
        }

        if(isset($request->modal_number)){
            $dataArr['modal_number'] = $request->modal_number;
        }

        if(isset($request->brand_color)){
            $dataArr['brand_color'] = $request->brand_color;
        }

        if(isset($request->primary_material_type)){
            $dataArr['primary_material_type'] = $request->primary_material_type;
        }

        if(isset($request->size)){
            $dataArr['size'] = $request->size;
        }

        if(isset($request->color)){
            $dataArr['color'] = $request->color;
        }

        if(isset($request->suitable_for)){
            $dataArr['suitable_for'] = $request->suitable_for;
        }

        if(isset($request->primary_material)){
            $dataArr['primary_material'] = $request->primary_material;
        }

        if(isset($request->delivery_condition)){
            $dataArr['delivery_condition'] = $request->delivery_condition;
        }

        if(isset($request->age_group)){
            $dataArr['age_group'] = $request->age_group;
        }

        if(isset($request->product_width)){
            $dataArr['product_width'] = $request->product_width.' '.($request->product_weight_unit ?? '');
        }

        if(isset($request->product_height)){
            $dataArr['product_height'] = $request->product_height;
        }

        if(isset($request->product_depth)){
            $dataArr['product_depth'] = $request->product_depth;
        }

        if(isset($request->product_weight)){
            $dataArr['product_weight'] = $request->product_weight;
        }

        if(isset($request->warranty_summary)){
            $dataArr['warranty_summary'] = $request->warranty_summary;
        }

        if(isset($request->covered_warranty)){
            $dataArr['covered_warranty'] = $request->covered_warranty;
        }

        if(isset($request->not_covered_warranty)){
            $dataArr['not_covered_warranty'] = $request->not_covered_warranty;
        }

        //echo '<pre>';print_r($dataArr);die();
        DB::table('listingdatas')->updateOrInsert( ['listing_id' => $listing_id], $dataArr );
        
        //$listing = DB::table('listings')->where('unique_id', $request->listing_id)->first();
        $categoryData = Category::where('id', $listing->category_id)->first();
        $brandData = Brand::where('brand_id', $listing->brand_id)->first();
        return redirect()->route('createListing', ['vertical'=>$categoryData->slug, 'brand'=>$brandData->brand_name, 'id'=>$listing->unique_id]);
        //print_r($dataArr);
        //die();
    }

    public function myListingPhotoStore(Request $request) {
        $listing_id = $request->listing_id;
        $dataArr = array();

        $listingPhotos = DB::table('listingphotos')->where('listing_id',$listing_id)->first();
        if($request->file('image_1')){
            $image1 = $this->uploadImage($request->image_1, '/uploads/listings/'.$listing_id);
            if( $listingPhotos && is_file(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_1)) ) {
                unlink(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_1));
            }
            $dataArr['image_1'] = $image1;
        }

        if($request->file('image_2')){
            $image2 = $this->uploadImage($request->image_2, '/uploads/listings/'.$listing_id);
            if( $listingPhotos && is_file(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_2)) ) {
                unlink(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_2));
            }
            $dataArr['image_2'] = $image2;
        }

        if($request->file('image_3')){
            $image3 = $this->uploadImage($request->image_3, '/uploads/listings/'.$listing_id);
            if( $listingPhotos && is_file(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_3)) ) {
                unlink(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_3));
            }
            $dataArr['image_3'] = $image3;
        }

        if($request->file('image_4')){
            $image4 = $this->uploadImage($request->image_4, '/uploads/listings/'.$listing_id);
            if( $listingPhotos && is_file(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_4)) ) {
                unlink(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_4));
            }
            $dataArr['image_4'] = $image4;
        }

        if($request->file('image_5')){
            $image5 = $this->uploadImage($request->image_5, '/uploads/listings/'.$listing_id);
            if( $listingPhotos && is_file(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_5)) ) {
                unlink(public_path('/uploads/listings/'.$listing_id.'/'.$listingPhotos->image_5));
            }
            $dataArr['image_5'] = $image5;
        }

        //$dataArr = array('image_2'=>$image2, 'image_3'=>$image3, 'image_4'=>$image4, 'image_5'=>$image5);
        DB::table('listingphotos')->updateOrInsert( ['listing_id' => $listing_id], $dataArr );

        $listing = DB::table('listings')->where('id', $listing_id)->first(); 
        $categoryData = Category::where('id', $listing->category_id)->first();
        $brandData = Brand::where('brand_id', $listing->brand_id)->first();

        return redirect()->route('createListing', ['vertical'=>$categoryData->slug, 'brand'=>$brandData->brand_name, 'id'=>$listing->unique_id]);
    }

    public function addListing()
    {
        $user_id = Auth::user()->id;
        $arguments = array('user_id'=> $user_id, 'type' => 'draft');
        $listings = Seller::listing($arguments);
        return view('seller.listing.list', ['listings'=>$listings, 'type'=>'draft']);
    }

    public function myListing()
    {
        $user_id = Auth::user()->id;
        $arguments = array('user_id'=> $user_id, 'type' => 'mylist');
        $listings = Seller::listing($arguments);
        return view('seller.listing.list', ['listings'=>$listings, 'type'=>'mylist']);
    }

    public function listingStatusChange(Request $request, $id)
    {
        $listing = DB::table('listings')->where('id', $id)->first();
        $arguments = array('listing_id'=> $listing->unique_id, 'status' => $request->status);
        $status = Seller::listingStatusChange($arguments);
    }

    /** Admin **/
    public function adminListingApproval(Request $request)
    {
        $user_id = Auth::user()->id;
        $arguments = array('type' => 'qc', 'user_role'=>Auth::user()->menuroles);
        $listings = Seller::listing($arguments);
        return view('seller.listing.list', ['listings'=>$listings, 'type'=>'qc', 'admin'=>true]);  //1=true
    }

    public function listingMenuMapping(Request $request)
    {
        $listing_id = $request->id;
        $menu_list = Seller::listingMenus(array('menu_id'=>2));
        $listingData = DB::table('listings')->where('unique_id',$listing_id)->first();   
        return view('seller.listing.menuMap', ['menu_list'=>$menu_list, 'listing'=>$listingData]);  //1=true   
    }

    public function listingMenuMappingStore(Request $request)
    {
        $menu_id = $request->menu_id;
        $hastags = $request->hastags ? implode(',', $request->hastags) : NULL;
        $listing_id = $request->listing_id;
        
        DB::table('listings')->where('id',$listing_id)->update(['menu_id'=>$menu_id, 'hastags'=>$hastags]);

        return redirect('/listing');
    }

    public function storeListingSizeChartData(Request $request)
    {
        $request->validate([
            'listing_id'  => 'required|integer',
            'unit'        => 'required|array|min:1',
            'unit.*'      => 'required|string',
            'price'       => 'required|array',
            'quantity'    => 'required|array',
            'brand_size'  => 'nullable|array',
            'sizeUnit'    => 'nullable|string',
        ]);

        $units     = $request->unit;
        $brandSize = $request->brand_size ?? [];
        $price     = $request->price;
        $quantity  = $request->quantity;

        // Per-row check: a checked size must have a matching numeric price/quantity.
        // (The form renders price[]/quantity[] for every row including unchecked ones,
        // so we can't validate price.* required globally — only for indices in unit[].)
        $errors = [];
        foreach ($units as $key => $value) {
            if (! isset($price[$key]) || $price[$key] === '' || ! is_numeric($price[$key]) || $price[$key] < 0) {
                $errors["price.$key"] = "Please enter a valid price for size '{$value}'.";
            }
            if (! isset($quantity[$key]) || $quantity[$key] === '' || ! is_numeric($quantity[$key]) || $quantity[$key] < 0) {
                $errors["quantity.$key"] = "Please enter a valid quantity for size '{$value}'.";
            }
        }
        if (! empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }

        $sizeData = DB::table('listing_sizechart')->where('listing_id', $request->listing_id)->get();
        if($sizeData) {
            foreach($sizeData as $sKey=>$sValue) {
                if(!in_array($sValue->size, $request->unit)) {
                    DB::table('listing_sizechart')->where('listing_id', $request->listing_id)->where('size',$sValue->size)->delete();
                }
            }
        }

        if(isset($request->unit)) {
            foreach ($request->unit as $key => $value) {
                $sizeChart = array('sizeFor'=>$request->sizeUnit, 'size'=>$value, 'brand_size'=>$brandSize[$key] ?? null, 'price'=>$price[$key], 'quantity'=>$quantity[$key]);

                DB::table('listing_sizechart')->updateOrInsert( ['listing_id' => $request->listing_id, 'size' => $value], $sizeChart );
            }
        }
    }

    public function sellerStoreCreate(Request $request)
    {
        return view('seller.store.add');
    }
}
