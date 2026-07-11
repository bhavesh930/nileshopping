{{-- resources/views/seller/stores/products.blade.php --}}
@extends('dashboard.base')

@section('title', 'Manage Store Products')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Products: {{ $store->store_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('seller.stores.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Stores
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <ul class="nav nav-tabs" id="productTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="store-products-tab" data-toggle="tab" href="#store-products" role="tab">
                                Store Products ({{ $storeProducts->count() }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="add-product-tab" data-toggle="tab" href="#add-product" role="tab">
                                Add New Product
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="store-products" role="tabpanel">
                            @if($storeProducts->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                                <th>Product</th>
                                                <th>SKU</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>MRP</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($storeProducts as $inventory)
                                                <tr>
                                                    <td>
                                                        {{ $inventory->listing->listingdata->product_name ?? 'N/A' }}
                                                        <br>
                                                        <small class="text-muted">ID: {{ $inventory->id }}</small>
                                                    </td>
                                                    <td>{{ $inventory->listing->listingdata->sku ?? 'N/A' }}</td>
                                                    <td>
                                                        <form action="{{ url('/seller/stores/' . $store->id . '/update-quantity/' . $inventory->id) }}" 
                                                              method="POST" class="form-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="number" name="quantity" value="{{ $inventory->quantity }}" 
                                                                   class="form-control form-control-sm" style="width: 80px;" min="0">
                                                            <button type="submit" class="btn btn-sm btn-primary ml-1">
                                                                <i class="fa fa-save"></i> Update
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <form action="{{ url('/seller/stores/' . $store->id . '/update-price/' . $inventory->id) }}" 
                                                              method="POST" class="form-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="number" name="price" value="{{ $inventory->price }}" 
                                                                   class="form-control form-control-sm" style="width: 100px;" step="0.01">
                                                            <button type="submit" class="btn btn-sm btn-primary ml-1">
                                                                <i class="fa fa-save"></i> Update
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <form action="{{ url('/seller/stores/' . $store->id . '/update-mrp/' . $inventory->id) }}" 
                                                              method="POST" class="form-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="number" name="mrp" value="{{ $inventory->mrp }}" 
                                                                   class="form-control form-control-sm" style="width: 100px;" step="0.01">
                                                            <button type="submit" class="btn btn-sm btn-primary ml-1">
                                                                <i class="fa fa-save"></i> Update
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $inventory->is_available ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $inventory->is_available ? 'Available' : 'Out of Stock' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form action="{{ url('/seller/stores/' . $store->id . '/remove-product/' . $inventory->id) }}" 
                                                              method="POST" class="d-inline" 
                                                              onsubmit="return confirm('Are you sure you want to remove this product from the store?')">
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
                                    <i class="fa fa-info-circle"></i> No products added to this store yet. Add products from the "Add New Product" tab.
                                </div>
                            @endif
                        </div>
                        
                        <div class="tab-pane fade" id="add-product" role="tabpanel">
                            <form action="{{ route('seller.stores.add-product', $store->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select Product <span class="text-danger">*</span></label>
                                            <select name="listing_id" class="form-control" required>
                                                <option value="">Select a product...</option>
                                                @foreach($listings as $listing)
                                                    @if(!in_array($listing->id, $existingProductIds))
                                                        <option value="{{ $listing->id }}">
                                                            {{ $listing->listingdata->product_name ?? 'N/A' }} 
                                                            (SKU: {{ $listing->listingdata->sku ?? 'N/A' }})
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @if($listings->count() == 0)
                                                <small class="text-danger">No products available. Please create products first.</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Initial Quantity <span class="text-danger">*</span></label>
                                            <input type="number" name="quantity" class="form-control" value="0" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Store Price (Optional)</label>
                                            <input type="number" name="price" class="form-control" step="0.01" placeholder="Override price">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Store MRP (Optional)</label>
                                            <input type="number" name="mrp" class="form-control" step="0.01" placeholder="Override MRP">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Add to Store</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection