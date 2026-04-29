@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i><?= ($type == 'draft' || $type == 'qc') ? '' : 'My';?> {{ __('Lising') }}</div>
                    <div class="card-body">
                        <div class="row"> 
                          <?php if($type == 'draft'):?>
                          <a href="{{ url('/seller') }}" class="btn btn-primary m-2">{{ __('Add Listing') }}</a>
                          <?php endif;?>
                        </div>
                        <br>
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th></th>
                            <th>Product Name</th>
                            <th>Vertical</th>
                            <th>Brand</th>
                            <th>SKU</th>
                            <th>Seller</th>
                            <th>Created_at</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($listings as $list)
                            <tr>
                              <td><a href="{{ url('/my/listing/create?vertical='.$list->category_slug.'&brand='.$list->brand.'&id='.$list->unique_id) }}"><i class="cil-file"></i></a></td>
                              <td><strong>{{ $list->product_name }}</strong></td>
                              <td><strong>{{ $list->vertical }}</strong></td>
                              <td>{{ $list->brand }}</td>
                              <td>{{ $list->sku }}</td>
                              <td>{{ $list->username }}</td>
                              <td>
                                  {{ $list->lcreated_at }}
                              </td>
                              <td>
                                <span class="btn btn-primary {{ (isset($admin) && $list->lStatus == 2) ? 'approval' : '' }}" data-rel="{{ (isset($admin)) ? $list->listing_id : '' }}">
                                <strong>{{ ($list->lStatus == 0) ? 'draft' : ($list->lStatus == '3' ? 'Approved' : ($list->lStatus == '1' ? 'Archive' : ($list->lStatus == '2' ? 'Under Approval' : 'QC') )) }}</strong>
                                </span>
                              </td>
                              
                              <!-- <td>
                                <a href="{{ url('/notes/' . $list->id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>
                              </td> -->
                              <td>
                                <!-- <form action="{{ route('notes.destroy', $list->id ) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-block btn-danger">Delete</button>
                                </form> -->
                                <span><?= (isset($admin)) ? '<i class="cil-sitemap mappingMenu" title="map menu" map-list="'.$list->unique_id.'"></i>' : '' ?></span>
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


        <div class="modal fade" id="MenuMappingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Product Description</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                      
                    </div>
                </div>
            </div>
        </div>
@endsection


@section('javascript')


<script type="text/javascript">
  $(document).on('click', '.approval', function(){
      var listing_id = $(this).attr('data-rel');
      if(listing_id) {
          listingStatusChange(listing_id);
      }
  });
  function listingStatusChange(id) {
      $.ajax({
          headers: {'x-csrf-token': '{{ csrf_token() }}' },
          method: 'post',
          url: "{{ url('') }}/listing/status/change/"+id,
          data: {status:3},
          success:function(result){
              $('td').find('[data-rel="'+id+'"]').removeClass('approval');
              $('td').find('[data-rel="'+id+'"]').find('strong').text('Approved');   
          }
      });
  }

  $(document).on('click', '.mappingMenu', function() {
      var listing_id = $(this).attr('map-list');
      $.ajax({
          headers: {'x-csrf-token': '{{ csrf_token() }}' },
          method: 'post',
          url: "{{ url('') }}/listing/map/menu",
          data: {status:3, id:listing_id},
          success:function(result){
              $('#MenuMappingModal').modal();
              $('#MenuMappingModal').find('.modal-body').html(result);
          }
      });
  });
</script>
@endsection

