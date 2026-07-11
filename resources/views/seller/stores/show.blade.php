{{-- resources/views/seller/stores/show.blade.php --}}
@extends('dashboard.base')

@section('title', 'Store Details - ' . $store->store_name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Store Details: {{ $store->store_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('seller.stores.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Stores
                        </a>
                        <a href="{{ route('seller.stores.edit', $store->id) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit Store
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                @if($store->logo)
                                    <img src="{{ asset('storage/' . $store->logo) }}" 
                                         alt="{{ $store->store_name }}" 
                                         class="img-fluid rounded" style="max-height: 200px;">
                                @else
                                    <div class="bg-secondary d-inline-flex align-items-center justify-content-center rounded" 
                                         style="width: 200px; height: 200px;">
                                        <i class="fa fa-store fa-4x text-white"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h5>Store Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Store Name:</strong> {{ $store->store_name }}</p>
                                    <p><strong>Store Slug:</strong> {{ $store->store_slug }}</p>
                                    <p><strong>Email:</strong> {{ $store->store_email }}</p>
                                    <p><strong>Phone:</strong> {{ $store->store_phone }}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge {{ $store->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $store->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>Location Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Address:</strong> {{ $store->address }}</p>
                                    <p><strong>City:</strong> {{ $store->city }}</p>
                                    <p><strong>State:</strong> {{ $store->state }}</p>
                                    <p><strong>Country:</strong> {{ $store->country }}</p>
                                    <p><strong>Pincode:</strong> {{ $store->pincode }}</p>
                                    @if($store->latitude && $store->longitude)
                                        <p><strong>Coordinates:</strong> {{ $store->latitude }}, {{ $store->longitude }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>Store Description</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $store->description ?: 'No description provided.' }}</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $store->inventory->count() }}</h3>
                                            <p>Total Products</p>
                                            <a href="{{ route('seller.stores.products', $store->id) }}" class="btn btn-light btn-sm">
                                                Manage Products
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $store->employees->count() }}</h3>
                                            <p>Total Employees</p>
                                            <a href="{{ route('seller.stores.employees', $store->id) }}" class="btn btn-light btn-sm">
                                                Manage Employees
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection