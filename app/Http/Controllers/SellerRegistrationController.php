<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Seller;
use DB;
use Auth;

class SellerRegistrationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Display a registration form for seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()){
            if(Auth::user()->menuroles == 'seller') {
                return redirect()->route('sellerDashboard');
            }
            return redirect()->route('listing');
        }
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
            'phone'         => 'required|digits:10|numeric|unique:sellers',         //|unique:sellers
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

        // if ($validatedData->fails()) {
        //     return redirect('seller/account')
        //                 ->withErrors($validatedData)
        //                 ->withInput();
        // }

        $user =  User::insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => date('Y-m-d H:i:s'),
            'menuroles' => 'seller',
        ]);

        // Assign Spatie role
        $user->assignRole('seller');

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
}