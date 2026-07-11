{{-- resources/views/seller/stores/employee-edit.blade.php --}}
@extends('dashboard.base')

@section('title', 'Edit Employee')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Employee: {{ $employee->user->name }}</h3>
                </div>
                <form action="{{ route('seller.stores.employees.update', [$store->id, $employee->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control" value="{{ $employee->user->name }}" disabled>
                                    <small class="text-muted">Name cannot be changed</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" value="{{ $employee->user->email }}" disabled>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Employee Code</label>
                                    <input type="text" class="form-control" value="{{ $employee->employee_code }}" disabled>
                                    <small class="text-muted">Employee code cannot be changed</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Designation</label>
                                    <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" 
                                           value="{{ old('designation', $employee->designation) }}" placeholder="e.g., Store Manager, Sales Associate">
                                    @error('designation')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="row">
                                @php
                                    $currentPermissions = is_array($employee->permissions) ? $employee->permissions : 
                                        ($employee->permissions ? json_decode($employee->permissions, true) : []);
                                @endphp
                                
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="manage_products" 
                                               class="form-check-input" id="perm_products"
                                               {{ in_array('manage_products', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_products">
                                            Manage Products (Add/Edit/Delete)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="manage_inventory" 
                                               class="form-check-input" id="perm_inventory"
                                               {{ in_array('manage_inventory', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_inventory">
                                            Manage Inventory (Stock Updates)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="manage_orders" 
                                               class="form-check-input" id="perm_orders"
                                               {{ in_array('manage_orders', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_orders">
                                            Manage Orders
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" value="view_reports" 
                                               class="form-check-input" id="perm_reports"
                                               {{ in_array('view_reports', $currentPermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_reports">
                                            View Reports
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <small class="form-text text-muted">Select the permissions this employee should have</small>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" {{ $employee->is_active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Employee Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                        <a href="{{ route('seller.stores.employees', $store->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection