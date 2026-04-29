<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeMiddleware
{
    public function handle(Request $request, Closure $next, $permission = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user is employee
        if ($user->menuroles == 'employee') {
            $employee = \App\Models\StoreEmployee::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if (!$employee) {
                abort(403, 'Unauthorized access. Employee record not found.');
            }
            
            // Store employee info in request for easy access
            $request->merge(['current_employee' => $employee, 'current_store' => $employee->store]);
            
            // Check specific permission if required
            if ($permission && !$employee->hasPermission($permission)) {
                abort(403, 'You do not have permission to access this page.');
            }
            
            return $next($request);
        }
        
        // If not employee, continue with normal flow
        return $next($request);
    }
}