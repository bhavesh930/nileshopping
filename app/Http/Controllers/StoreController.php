<?php
// app/Http/Controllers/StoreController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Listing;
use App\Models\StoreInventory;
use App\Models\User;
use App\Models\StoreEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display list of stores for the seller or employee
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('seller')) {
            $stores = Store::where('seller_id', $user->id)->latest()->get();
        } elseif ($user->hasRole('employee')) {
            // Get the store associated with this employee
            $employee = StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if ($employee) {
                $stores = Store::where('id', $employee->store_id)->latest()->get();
            } else {
                $stores = collect();
            }
        } else {
            $stores = collect();
        }
        
        return view('seller.stores.index', compact('stores'));
    }

    /**
     * Show form to create new store (Sellers only)
     */
    public function create()
    {
        // Only sellers can create stores
        if (!Auth::user()->hasRole('seller')) {
            abort(403, 'Only sellers can create stores.');
        }
        return view('seller.stores.create');
    }

    /**
     * Store a new store (Sellers only)
     */
    public function store(Request $request)
    {
        // Only sellers can create stores
        if (!Auth::user()->hasRole('seller')) {
            abort(403, 'Only sellers can create stores.');
        }
        
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'store_email' => 'required|email|max:255',
            'store_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $storeData = $validated;
            $storeData['seller_id'] = Auth::user()->id;
            $storeData['store_slug'] = Str::slug($request->store_name . '-' . uniqid());

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('stores/logos', 'public');
                $storeData['logo'] = $logoPath;
            }

            if ($request->hasFile('cover_image')) {
                $coverPath = $request->file('cover_image')->store('stores/covers', 'public');
                $storeData['cover_image'] = $coverPath;
            }

            Store::create($storeData);

            DB::commit();

            return redirect()->route('seller.stores.index')
                ->with('success', 'Store created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating store: ' . $e->getMessage());
        }
    }

    /**
     * Show store details
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if ($user->hasRole('seller')) {
            $store = Store::with(['employees.user', 'inventory.listing'])
                ->where('seller_id', $user->id)
                ->findOrFail($id);
        } elseif ($user->hasRole('employee')) {
            $employee = StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if (!$employee || $employee->store_id != $id) {
                abort(404, 'Store not found or you do not have access.');
            }
            
            $store = Store::with(['inventory.listing'])->findOrFail($id);
        } else {
            abort(403, 'Unauthorized access.');
        }
        
        return view('seller.stores.show', compact('store'));
    }

    /**
     * Show form to edit store (Sellers only)
     */
    public function edit($id)
    {
        // Only sellers can edit stores
        if (!Auth::user()->hasRole('seller')) {
            abort(403, 'Only sellers can edit stores.');
        }
        
        $store = Store::where('seller_id', Auth::user()->id)->findOrFail($id);
        return view('seller.stores.edit', compact('store'));
    }

    /**
     * Update store (Sellers only)
     */
    public function update(Request $request, $id)
    {
        // Only sellers can update stores
        if (!Auth::user()->hasRole('seller')) {
            abort(403, 'Only sellers can update stores.');
        }
        
        $store = Store::where('seller_id', Auth::user()->id)->findOrFail($id);

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'store_email' => 'required|email|max:255',
            'store_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('stores/logos', 'public');
                $validated['logo'] = $logoPath;
            }

            if ($request->hasFile('cover_image')) {
                $coverPath = $request->file('cover_image')->store('stores/covers', 'public');
                $validated['cover_image'] = $coverPath;
            }

            $store->update($validated);

            DB::commit();

            return redirect()->route('seller.stores.index')
                ->with('success', 'Store updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating store: ' . $e->getMessage());
        }
    }

    /**
     * Delete store (Sellers only)
     */
    public function destroy($id)
    {
        // Only sellers can delete stores
        if (!Auth::user()->hasRole('seller')) {
            abort(403, 'Only sellers can delete stores.');
        }
        
        $store = Store::where('seller_id', Auth::user()->id)->findOrFail($id);
        
        try {
            $store->delete();
            return redirect()->route('seller.stores.index')
                ->with('success', 'Store deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting store: ' . $e->getMessage());
        }
    }

    /**
     * Manage products for a store (Supports both sellers and employees with permissions)
     */
    public function manageProducts($storeId)
    {
        $user = Auth::user();
        $store = null;
        $sellerId = null;
        
        // Handle Seller access
        if ($user->hasRole('seller')) {
            $store = Store::where('seller_id', $user->id)->findOrFail($storeId);
            $sellerId = $user->id;
        } 
        // Handle Employee access
        elseif ($user->hasRole('employee')) {
            // Get employee record
            $employee = StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if (!$employee) {
                abort(404, 'Employee record not found.');
            }
            
            // Check if employee belongs to this store
            if ($employee->store_id != $storeId) {
                abort(404, 'Store not found or you do not have access.');
            }
            
            // Check if employee has permission to manage products
            $permissions = is_array($employee->permissions) ? $employee->permissions : json_decode($employee->permissions, true);
            if (!in_array('manage_products', $permissions)) {
                abort(403, 'You do not have permission to manage products.');
            }
            
            // Get the store
            $store = Store::findOrFail($storeId);
            $sellerId = $store->seller_id;
        } 
        else {
            abort(403, 'Unauthorized access.');
        }
        
        // Get all listings for the seller
        $listings = Listing::with('listingdata')->where('user_id', $sellerId)
            ->where('status', 3) // Active listings
            ->get();
        
        // Get products already mapped to this store
        $storeProducts = StoreInventory::where('store_id', $storeId)
            ->with('listing')
            ->get();
        
        // Create a separate array for quick lookup
        $existingProductIds = $storeProducts->pluck('listing_id')->toArray();
        
        return view('seller.stores.products', compact('store', 'listings', 'storeProducts', 'existingProductIds'));
    }

    /**
     * Add product to store (Supports both sellers and employees with permissions)
     */
    public function addProductToStore(Request $request, $storeId)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $store = null;
        $sellerId = null;
        
        // Handle Seller access
        if ($user->hasRole('seller')) {
            $store = Store::where('seller_id', $user->id)->findOrFail($storeId);
            $sellerId = $user->id;
        } 
        // Handle Employee access
        elseif ($user->hasRole('employee')) {
            $employee = StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if (!$employee || $employee->store_id != $storeId) {
                abort(404, 'Store not found or you do not have access.');
            }
            
            // Check permission
            $permissions = is_array($employee->permissions) ? $employee->permissions : json_decode($employee->permissions, true);
            if (!in_array('manage_products', $permissions)) {
                abort(403, 'You do not have permission to add products.');
            }
            
            $store = Store::findOrFail($storeId);
            $sellerId = $store->seller_id;
        } 
        else {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if listing belongs to seller
        $listing = Listing::where('user_id', $sellerId)
            ->where('id', $request->listing_id)
            ->firstOrFail();

        try {
            StoreInventory::updateOrCreate(
                [
                    'store_id' => $storeId,
                    'listing_id' => $request->listing_id,
                ],
                [
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'mrp' => $request->mrp,
                    'is_available' => $request->quantity > 0,
                ]
            );

            return redirect()->route('seller.stores.products', $storeId)
                ->with('success', 'Product added to store successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error adding product: ' . $e->getMessage());
        }
    }

    /**
     * Update product quantity (Supports both sellers and employees with inventory permission)
     */
    public function updateProductQuantity(Request $request, $store, $inventory)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        
        // Handle Seller access
        if ($user->hasRole('seller')) {
            $storeModel = Store::where('seller_id', $user->id)->findOrFail($store);
        } 
        // Handle Employee access
        elseif ($user->hasRole('employee')) {
            $employee = StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if (!$employee || $employee->store_id != $store) {
                abort(404, 'Store not found or you do not have access.');
            }
            
            // Check inventory permission
            $permissions = is_array($employee->permissions) ? $employee->permissions : json_decode($employee->permissions, true);
            if (!in_array('manage_inventory', $permissions)) {
                abort(403, 'You do not have permission to manage inventory.');
            }
            
            $storeModel = Store::findOrFail($store);
        } 
        else {
            abort(403, 'Unauthorized access.');
        }
        
        $inventoryModel = StoreInventory::where('store_id', $store)
            ->where('id', $inventory)
            ->firstOrFail();

        try {
            $inventoryModel->update([
                'quantity' => $request->quantity,
                'is_available' => $request->quantity > 0,
            ]);

            return redirect()->route('seller.stores.products', $store)
                ->with('success', 'Quantity updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating quantity: ' . $e->getMessage());
        }
    }

    /**
     * Update product price (Supports both sellers and employees with product management permission)
     */
    public function updateProductPrice(Request $request, $store, $inventory)
    {
        $request->validate([
            'price' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        
        if ($user->hasRole('seller')) {
            $storeModel = Store::where('seller_id', $user->id)->findOrFail($store);
        } 
        elseif ($user->hasRole('employee')) {
            $employee = StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if (!$employee || $employee->store_id != $store) {
                abort(404, 'Store not found or you do not have access.');
            }
            
            $permissions = is_array($employee->permissions) ? $employee->permissions : json_decode($employee->permissions, true);
            if (!in_array('manage_products', $permissions)) {
                abort(403, 'You do not have permission to update prices.');
            }
            
            $storeModel = Store::findOrFail($store);
        } 
        else {
            abort(403, 'Unauthorized access.');
        }
        
        $inventoryModel = StoreInventory::where('store_id', $store)
            ->where('id', $inventory)
            ->firstOrFail();

        try {
            $inventoryModel->update(['price' => $request->price]);

            return redirect()->route('seller.stores.products', $store)
                ->with('success', 'Price updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating price: ' . $e->getMessage());
        }
    }

    /**
     * Update product MRP (Supports both sellers and employees with product management permission)
     */
    public function updateProductMRP(Request $request, $store, $inventory)
    {
        $request->validate([
            'mrp' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        
        if ($user->hasRole('seller')) {
            $storeModel = Store::where('seller_id', $user->id)->findOrFail($store);
        } 
        elseif ($user->hasRole('employee')) {
            $employee = StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if (!$employee || $employee->store_id != $store) {
                abort(404, 'Store not found or you do not have access.');
            }
            
            $permissions = is_array($employee->permissions) ? $employee->permissions : json_decode($employee->permissions, true);
            if (!in_array('manage_products', $permissions)) {
                abort(403, 'You do not have permission to update MRP.');
            }
            
            $storeModel = Store::findOrFail($store);
        } 
        else {
            abort(403, 'Unauthorized access.');
        }
        
        $inventoryModel = StoreInventory::where('store_id', $store)
            ->where('id', $inventory)
            ->firstOrFail();

        try {
            $inventoryModel->update(['mrp' => $request->mrp]);

            return redirect()->route('seller.stores.products', $store)
                ->with('success', 'MRP updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating MRP: ' . $e->getMessage());
        }
    }

    /**
     * Remove product from store (Supports both sellers and employees with product management permission)
     */
    public function removeProductFromStore($storeId, $inventoryId)
    {
        $user = Auth::user();
        
        if ($user->hasRole('seller')) {
            $store = Store::where('seller_id', $user->id)->findOrFail($storeId);
        } 
        elseif ($user->hasRole('employee')) {
            $employee = StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if (!$employee || $employee->store_id != $storeId) {
                abort(404, 'Store not found or you do not have access.');
            }
            
            $permissions = is_array($employee->permissions) ? $employee->permissions : json_decode($employee->permissions, true);
            if (!in_array('manage_products', $permissions)) {
                abort(403, 'You do not have permission to remove products.');
            }
            
            $store = Store::findOrFail($storeId);
        } 
        else {
            abort(403, 'Unauthorized access.');
        }
        
        $inventory = StoreInventory::where('store_id', $storeId)
            ->where('id', $inventoryId)
            ->firstOrFail();

        try {
            $inventory->delete();
            return redirect()->route('seller.stores.products', $storeId)
                ->with('success', 'Product removed from store successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error removing product: ' . $e->getMessage());
        }
    }
}