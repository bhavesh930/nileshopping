@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('Lising') }}</div>
                    <div class="card-body">
                        <div class="row">
                        </div>
                        <br>
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th></th>
                            <th>Vertical</th>
                            <th>Brand</th>
                            <th>SKU</th>
                            <th>Created_at</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($listings as $list)
                            <tr>
                              <td><a href="{{ url('/my/listing/create?vertical='.$list->category_slug.'&brand='.$list->brand.'&id='.$list->unique_id) }}"><i class="cil-file"></i></a></td>
                              <td><strong>{{ $list->vertical }}</strong></td>
                              <td>{{ $list->brand }}</td>
                              <td>{{ $list->sku }}</td>
                              <td>
                                  {{ $list->lcreated_at }}
                              </td>
                              <td><strong>{{ ($list->lStatus == 0) ? 'draft' : ($list->lStatus == '3' ? 'Approve' : ($list->lStatus == '1' ? 'Archive' : ($list->lStatus == '2' ? 'Under Approval' : 'QC') )) }}</strong></td>
                              
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

