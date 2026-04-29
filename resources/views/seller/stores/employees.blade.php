{{-- resources/views/seller/stores/employees.blade.php --}}
@extends('dashboard.base')

@section('title', 'Store Employees')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employees: {{ $store->store_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('seller.stores.employees.create', $store->id) }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Employee
                        </a>
                        <a href="{{ route('seller.stores.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if($employees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Employee Code</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Designation</th>
                                        <th>Permissions</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td>{{ $employee->employee_code }}</td>
                                            <td>{{ $employee->user->name }}</td>
                                            <td>{{ $employee->user->email }}</td>
                                            <td>{{ $employee->designation ?: 'N/A' }}</td>
                                            <td>
                                                @if($employee->permissions)
                                                    @foreach($employee->permissions as $permission)
                                                        <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $permission)) }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No permissions</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $employee->is_active ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('seller.stores.employees.edit', [$store->id, $employee->id]) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('seller.stores.employees.destroy', [$store->id, $employee->id]) }}" 
                                                      method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to remove this employee?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No employees added yet. <a href="{{ route('seller.stores.employees.create', $store->id) }}">Add your first employee</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection