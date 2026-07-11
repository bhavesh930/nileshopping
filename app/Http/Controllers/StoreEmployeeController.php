<?php
// app/Http/Controllers/StoreEmployeeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreEmployee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreEmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('employee.permission:manage_products')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * List employees for a store
     */
    public function index($storeId)
    {
        $store = Store::where('seller_id', Auth::user()->id)->findOrFail($storeId);
        $employees = StoreEmployee::where('store_id', $storeId)
            ->with('user')
            ->latest()
            ->get();
        
        return view('seller.stores.employees', compact('store', 'employees'));
    }

    /**
     * Show form to create employee
     */
    public function create($storeId)
    {
        $store = Store::where('seller_id', Auth::user()->id)->findOrFail($storeId);
        $permissions = $this->getPermissionsList();
        
        return view('seller.stores.employee-create', compact('store', 'permissions'));
    }

    /**
     * Store employee
     */
    public function store(Request $request, $storeId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'designation' => 'nullable|string|max:100',
            'permissions' => 'array',
            'employee_code' => 'nullable|string|unique:store_employees,employee_code',
        ]);

        $store = Store::where('seller_id', Auth::user()->id)->findOrFail($storeId);

        try {
            DB::beginTransaction();

            // Create user account for employee
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'menuroles' => 'employee',
                'email_verified_at' => now(),
            ]);

            // Assign the employee role using Spatie
            $user->assignRole('employee');

            // Generate employee code if not provided
            $employeeCode = $request->employee_code ?: 'EMP' . strtoupper(uniqid());

            // Create store employee record
            StoreEmployee::create([
                'store_id' => $storeId,
                'user_id' => $user->id,
                'employee_code' => $employeeCode,
                'designation' => $request->designation,
                'permissions' => $request->permissions ?? [],
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('seller.stores.employees', $storeId)
                ->with('success', 'Employee added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding employee: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit employee
     */
    public function edit($storeId, $employeeId)
    {
        $store = Store::where('seller_id', Auth::user()->id)->findOrFail($storeId);
        $employee = StoreEmployee::where('store_id', $storeId)
            ->with('user')
            ->findOrFail($employeeId);
        $permissions = $this->getPermissionsList();
        
        return view('seller.stores.employee-edit', compact('store', 'employee', 'permissions'));
    }

    /**
     * Update employee
     */
    public function update(Request $request, $storeId, $employeeId)
    {
        $request->validate([
            'designation' => 'nullable|string|max:100',
            'permissions' => 'array',
            'is_active' => 'sometimes|boolean',
        ]);

        $store = Store::where('seller_id', Auth::user()->id)->findOrFail($storeId);
        $employee = StoreEmployee::where('store_id', $storeId)->findOrFail($employeeId);

        try {
            $employee->update([
                'designation' => $request->designation,
                'permissions' => $request->permissions ?? [],
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('seller.stores.employees', $storeId)
                ->with('success', 'Employee updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    /**
     * Delete employee
     */
    public function destroy($storeId, $employeeId)
    {
        $store = Store::where('seller_id', Auth::user()->id)->findOrFail($storeId);
        $employee = StoreEmployee::where('store_id', $storeId)->findOrFail($employeeId);

        try {
            // Delete user account if it's not the seller
            if ($employee->user_id != Auth::user()->id) {
                $employee->user->delete();
            }
            $employee->delete();

            return redirect()->route('seller.stores.employees', $storeId)
                ->with('success', 'Employee removed successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error removing employee: ' . $e->getMessage());
        }
    }

    /**
     * Get list of available permissions
     */
    private function getPermissionsList()
    {
        return [
            'manage_products' => 'Manage Products (Add/Edit/Delete)',
            'manage_inventory' => 'Manage Inventory (Stock Updates)',
            'manage_orders' => 'Manage Orders',
            'view_reports' => 'View Reports',
        ];
    }
}