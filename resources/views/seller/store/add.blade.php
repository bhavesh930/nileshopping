@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12">
                 @if (Session::has('errors'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h4 class="alert-heading">Error!</h4>
                        <p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </p>

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Create Store') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ isset($editData) ? route('category.update', $editData->id) : route('category.store') }}" class="row">
                            @csrf

                            @if(isset($editData))
                            @method('PUT')
                            <!-- <input type="hidden" name="id" value="{{ $editData->id }}" id="categoryId"/> -->
                            @endif
                            <div class="form-group col-md-6">
                                <label>Store Name</label>
                                <input class="form-control" type="text" placeholder="{{ __('Store Name') }}" name="name" value="<?= isset($editData) ? $editData->name : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Store Email</label>
                                <input class="form-control" type="text" placeholder="{{ __('Store Email') }}" name="email" value="<?= isset($editData) ? $editData->email : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Contact No.</label>
                                <input class="form-control" type="text" placeholder="{{ __('Contact no.') }}" name="contact" value="<?= isset($editData) ? $editData->contact : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>GST No.</label>
                                <input class="form-control" type="text" placeholder="{{ __('GST no.') }}" name="gst" value="<?= isset($editData) ? $editData->gst : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Latitude</label>
                                <input class="form-control" type="text" placeholder="{{ __('Latitude') }}" name="lat" value="<?= isset($editData) ? $editData->lat : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Longitude</label>
                                <input class="form-control" type="text" placeholder="{{ __('Longitude') }}" name="long" value="<?= isset($editData) ? $editData->long : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Address</label>
                                <input class="form-control" type="text" placeholder="{{ __('Address') }}" name="address" value="<?= isset($editData) ? $editData->address : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Landmark</label>
                                <input class="form-control" type="text" placeholder="{{ __('Landmark') }}" name="landmark" value="<?= isset($editData) ? $editData->landmark : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>City</label>
                                <input class="form-control" type="text" placeholder="{{ __('City') }}" name="city" value="<?= isset($editData) ? $editData->city : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>State</label>
                                <input class="form-control" type="text" placeholder="{{ __('State') }}" name="state" value="<?= isset($editData) ? $editData->state : ''?>" required autofocus>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Self Pickup</label>
                                <select name="selfPickup" class="form-control">
                                    <option value="1" selected>Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <button class="btn btn-block btn-success col-1" type="submit">{{ __('Add') }}</button>
                                <!-- <a href="{{ route('category.index') }}" class="btn btn-block btn-primary col-sm-3">{{ __('Return') }}</a>      -->
                            </div>
                        </form>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection

@section('javascript')

@endsection