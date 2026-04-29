{{-- resources/views/seller/stores/index.blade.php --}}
@extends('dashboard.base')

@section('title', 'My Stores')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Stores</h3>
                    <div class="card-tools">
                        <a href="{{ route('seller.stores.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Create New Store
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if($stores->count() > 0)
                        <div class="row">
                            @foreach($stores as $store)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        @if($store->logo)
                                            <img src="{{ asset('storage/' . $store->logo) }}" 
                                                 class="card-img-top" alt="{{ $store->store_name }}" 
                                                 style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                                                 style="height: 200px;">
                                                <i class="fa fa-store fa-4x text-white"></i>
                                            </div>
                                        @endif
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $store->store_name }}</h5>
                                            <p class="card-text text-muted small">
                                                <i class="fa fa-map-marker"></i> {{ $store->city }}, {{ $store->state }}
                                            </p>
                                            <p class="card-text">{{ Str::limit($store->description, 100) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge {{ $store->is_active ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $store->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                <div>
                                                    <a href="{{ route('seller.stores.show', $store->id) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('seller.stores.edit', $store->id) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100">
                                                <a href="{{ route('seller.stores.products', $store->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-box"></i> Products
                                                </a>
                                                <a href="{{ route('seller.stores.employees', $store->id) }}" 
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fa fa-users"></i> Employees
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            No stores found. <a href="{{ route('seller.stores.create') }}">Create your first store</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection