@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('Order List') }}</div>
                    <div class="card-body">
                        <div class="row">
                        </div>
                        <br>
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th></th>
                            <th>Order ID</th>
                            <th>Payment Mode</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($orders as $list)
                            <tr>
                              <td><a href="{{ url('/order/' . $list->order_id ) }}"><i class="cil-file"></i></a></td>
                              <td><strong>{{ $list->order_id }}</strong></td>
                              <td>{{ $list->payment_mode == 1 ? 'Cash' : ($list->payment_mode == 2 ? 'Online' : '') }}</td>
                              <td>{{ $list->grand_total }}</td>
                              <td>{{ $list->created_at }}</td>
                              <td></td>
                              
                              <!-- <td>
                                <a href="{{ url('/notes/' . $list->id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>
                              </td> -->
                              <td>
                                <!-- <form action="{{ route('notes.destroy', $list->id ) }}" method="POST">
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

