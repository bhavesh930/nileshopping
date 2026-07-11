<?php
// app/Http/Middleware/EmployeePermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();
        
        // If user is seller, allow all
        if ($user->menuroles == 'seller') {
            return $next($request);
        }
        
        // If user is employee, check permission
        if ($user->menuroles == 'employee') {
            $employee = \App\Models\StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if ($employee && $employee->hasPermission($permission)) {
                return $next($request);
            }
        }
        
        abort(403, 'You do not have permission to perform this action.');
    }
}