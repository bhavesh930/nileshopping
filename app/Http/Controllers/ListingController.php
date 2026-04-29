<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Listing;
use App\Models\Seller;
use DB;
use Auth;

class ListingController extends Controller
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
        $user_id = Auth::user()->id;
        $arguments = array('type' => 'qc');
        $listings = Seller::listing($arguments);
        return view('seller.listing.list', ['listings'=>$listings, 'type'=>'qc']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
     * Map listing to store during creation
     */
    public function createWithStore(Request $request)
    {
        $stores = Store::where('seller_id', Auth::user()->id)->active()->get();
        
        $vertical = $request->vertical;
        $brand = $request->brand;
        
        return view('seller.listing.create-with-store', compact('stores', 'vertical', 'brand'));
    }

    /**
     * Store listing with store mapping
     */
    public function storeWithStore(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,brand_id',
            'store_id' => 'required|exists:stores,id',
            'is_global' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create listing
            $listing = Listing::create([
                'user_id' => Auth::user()->id,
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'],
                'store_id' => $validated['store_id'],
                'is_global' => $validated['is_global'] ?? true,
                'unique_id' => time(),
                'status' => 0, // Draft
            ]);

            // Add to store inventory if store-specific
            if (!$listing->is_global && $listing->store_id) {
                DB::table('store_inventory')->insert([
                    'store_id' => $listing->store_id,
                    'listing_id' => $listing->id,
                    'quantity' => 0,
                    'is_available' => false,
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('createListing', [
                'vertical' => $request->vertical,
                'brand' => $request->brand,
                'id' => $listing->unique_id
            ])->with('success', 'Listing created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating listing: ' . $e->getMessage());
        }
    }

    /**
     * Show store selection for listing
     */
    public function selectStore()
    {
        $stores = Store::where('seller_id', Auth::user()->id)
            ->active()
            ->get();
        
        return view('seller.listing.select-store', compact('stores'));
    }

}
