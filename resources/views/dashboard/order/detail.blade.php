@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('Order Detail') }}</div>
                    <div class="card-body">
                        <div class="row">
                        </div>
                        <br>
                        
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th>Order ID</th>
                            <th>Product ID</th>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>MRP</th>
                            <th>Selling Price</th>
                            <th>HSN</th>
                            <th>Model Number</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($order_details as $detail)
                            <tr>
                                <td><strong>{{ $order_id }}</strong></td>
                                <td>{{ $detail->product_id }}</td>
                                <td>{{ $detail->category }}</td>
                                <td>{{ $detail->product_name }}</td>
                                <td>{{ $detail->sku }}</td>
                                <td>{{ $detail->mrp }}</td>
                                <td>{{ $detail->selling_price }}</td>
                                <td>{{ $detail->hsn }}</td>
                                <td>{{ $detail->modal_number }}</td>
                                <td>{{ $detail->status == 2 ? 'Pending' : ($detail->status == 3 ? 'Confirmed' : ($detail->status == 4 || $detail->status == 5 ? 'Cancelled' : ($detail->status == 6 ? 'Returned' : ($detail->status == 7 ? 'Delivered' : '') ) ) ) }}</td>
                                <td>
                                    @if($detail->status == 2 && Auth::user()->menuroles == 'seller')
                                        <form action="{{ route('orderStatusUpdate') }}" method="POST">
                                            @method('PUT')
                                            @csrf
                                            <input type="hidden" name="status" value="4" />
                                            <input type="hidden" name="cart_id" value="{{$detail->id}}" />
                                            <button class="btn btn-block btn-danger">Cancelled</button>
                                        </form>
                                        <form action="{{ route('orderStatusUpdate') }}" method="POST">
                                            @method('PUT')
                                            @csrf
                                            <input type="hidden" name="status" value="3" />
                                            <input type="hidden" name="cart_id" value="{{$detail->id}}" />
                                            <button class="btn btn-block btn-success">Confirmed</button>
                                        </form>
                                    @endif
                                </td>
                              
                              <!-- <td>
                                <a href="{{ url('/notes/' . $detail->id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>
                              </td> -->
                              <td>
                                <!-- <form action="{{ route('notes.destroy', $detail->id ) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-block btn-danger">Delete</button>
                                </form> -->
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection

