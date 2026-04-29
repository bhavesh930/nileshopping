@extends('dashboard.authBase')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mx-4">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('sellerCreate') }}">
                            @csrf
                            @if (Session::has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert" data-dismiss="alert">
                                    <h4 class="alert-heading">Success!</h4>
                                    <p>{{ Session::get('success') }}</p>

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if (Session::has('errors'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert" data-dismiss="alert">
                                    <h4 class="alert-heading">Error!</h4>
                                    <!-- <p>{{ Session::get('errors.email') }}</p> -->
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <h1>{{ __('Seller Registration') }}</h1>
                            <p class="text-muted">Create your account</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-building"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter Store owner name') }}" name="name" required autofocus value="{{ old('name') }}"><br/>
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-screen-smartphone"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter mobile number') }}" name="phone" required value="{{ old('phone') }}" autofocus><br/>
                                <span class="text-danger">{{ $errors->first('phone') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-envelope-open"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('E-Mail Address') }}" name="email" value="{{ old('email') }}" required><br/>
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-lock-locked"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="password" placeholder="{{ __('Password') }}" name="password" required><br/>
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-lock-locked"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="password" placeholder="{{ __('Confirm Password') }}" name="password_confirmation" required>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-user"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter Store name') }}" name="storeName" value="{{ old('storeName') }}" required autofocus><br/>
                                <span class="text-danger">{{ $errors->first('storeName') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-envelope-open"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter Store Email') }}" name="storeEmail" value="{{ old('storeEmail') }}" required autofocus><br/>
                                <span class="text-danger">{{ $errors->first('storeEmail') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-screen-smartphone"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter Store Mobile') }}" name="storePhone" value="{{ old('storePhone') }}" required autofocus><br/>
                                <span class="text-danger">{{ $errors->first('storePhone') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-satelite"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter Country') }}" name="country" value="{{ old('country') }}" required autofocus><br/>
                                <span class="text-danger">{{ $errors->first('country') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-satelite"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter State') }}" name="state" value="{{ old('state') }}" required autofocus><br/>
                                <span class="text-danger">{{ $errors->first('state') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-satelite"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter City') }}" name="city" value="{{ old('city') }}" required autofocus><br/>
                                <span class="text-danger">{{ $errors->first('city') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-fax"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter Pincode') }}" name="pincode" value="{{ old('pincode') }}" required autofocus><br/>
                                <span class="text-danger">{{ $errors->first('pincode') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-address-book"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter Address') }}" name="address" value="{{ old('address') }}" required autofocus><br/>
                                <span class="text-danger">{{ $errors->first('address') }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <svg class="c-icon">
                                            <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-user"></use>
                                        </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Enter Employee Code') }}" name="employeeCode" value="{{ old('employeeCode') }}" required autofocus><br/>
                                <span class="text-danger">{{ $errors->first('employeeCode') }}</span>
                            </div>
                            <button class="btn btn-block btn-success" type="submit">{{ __('Register') }}</button>
                        </form>
                    </div>
                
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')

@endsection