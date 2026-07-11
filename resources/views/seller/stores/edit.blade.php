{{-- resources/views/seller/stores/edit.blade.php --}}
@extends('dashboard.base')

@section('title', 'Edit Store - ' . $store->store_name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Store: {{ $store->store_name }}</h3>
                </div>
                <form action="{{ route('seller.stores.update', $store->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Store Name <span class="text-danger">*</span></label>
                                    <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror" 
                                           value="{{ old('store_name', $store->store_name) }}" required>
                                    @error('store_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Store Email <span class="text-danger">*</span></label>
                                    <input type="email" name="store_email" class="form-control @error('store_email') is-invalid @enderror" 
                                           value="{{ old('store_email', $store->store_email) }}" required>
                                    @error('store_email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Store Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="store_phone" class="form-control @error('store_phone') is-invalid @enderror" 
                                           value="{{ old('store_phone', $store->store_phone) }}" required>
                                    @error('store_phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pincode <span class="text-danger">*</span></label>
                                    <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" 
                                           value="{{ old('pincode', $store->pincode) }}" required>
                                    @error('pincode')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address', $store->address) }}</textarea>
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                           value="{{ old('city', $store->city) }}" required>
                                    @error('city')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State <span class="text-danger">*</span></label>
                                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" 
                                           value="{{ old('state', $store->state) }}" required>
                                    @error('state')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Country <span class="text-danger">*</span></label>
                                    <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" 
                                           value="{{ old('country', $store->country) }}" required>
                                    @error('country')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Latitude</label>
                                    <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror" 
                                           value="{{ old('latitude', $store->latitude) }}" placeholder="e.g., 23.0225">
                                    @error('latitude')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Optional - for location-based services</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Longitude</label>
                                    <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" 
                                           value="{{ old('longitude', $store->longitude) }}" placeholder="e.g., 72.5714">
                                    @error('longitude')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Optional - for location-based services</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $store->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Store Logo</label>
                                    @if($store->logo)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $store->logo) }}" alt="Current Logo" style="height: 100px;">
                                        </div>
                                    @endif
                                    <input type="file" name="logo" class="form-control-file @error('logo') is-invalid @enderror">
                                    @error('logo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Recommended size: 200x200px. Leave empty to keep current logo.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cover Image</label>
                                    @if($store->cover_image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $store->cover_image) }}" alt="Current Cover" style="height: 100px;">
                                        </div>
                                    @endif
                                    <input type="file" name="cover_image" class="form-control-file @error('cover_image') is-invalid @enderror">
                                    @error('cover_image')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Recommended size: 1200x400px. Leave empty to keep current cover.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" {{ $store->is_active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Store Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Store</button>
                        <a href="{{ route('seller.stores.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection