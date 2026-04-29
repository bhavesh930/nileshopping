@extends('dashboard.base')

@section('content')

<?php 
$usrRoleArr = explode(',', $user_role);
?>

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Brand Lising') }}</div>
                    <div class="card-body">
                        <div class="row"> 
                        </div>
                        <br>
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th>Brand Name</th>
                            <th>Seller Name</th>
                            <th>Brand Logo</th>
                            <th>Brand Owner</th>
                            <th>Document</th>
                            <th>Website</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($brandList as $list)
                            <?php 
                            $userData = \App\Models\User::where('id', $list->user_id)->first();
                            ?>
                            <tr>
                              <td>{{ $list->brand_name }}</td>
                              <td>{{ $userData->name }} {{ $userData->lastname }}</td>
                              <td><a href="<?= ($list->brand_logo) ? url('/').'/uploads/brands/'.$list->brand_logo : '' ?>" target="_blank"><img src="<?= ($list->brand_logo) ? url('/').'/uploads/brands/'.$list->brand_logo : '' ?>" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" width="75" height="75" style="object-fit: contain; text-align: center;"  /></a> </td>
                              <td>{{ $list->brand_owner == 1 ? 'Yes' : 'No' }}</td>
                              <td><a href="<?= ($list->trademark_doc) ? url('/').'/uploads/brands/'.$list->trademark_doc : '' ?>" target="_blank">{{ $list->document_type ? $list->document_type : '' }}</a></td>
                              <td>{{ $list->website_link }}</td>
                              <td>
                                <span class="btn btn-primary {{ (isset($user_role) && in_array('admin', $usrRoleArr)) ? 'approval' : '' }}" data-rel="{{ (isset($user_role) && in_array('admin', $usrRoleArr)) ? $list->id : '' }}">
                                <strong>{{ ($list->status == 1) ? 'Approved' : ($list->status == '2' ? 'Pending' : ($list->status == '3' ? 'Rejected' : '' )) }}</strong>
                                </span>
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

<?php 
if(isset($user_role) && in_array('admin', $usrRoleArr)) {?>
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
          url: "{{ url('') }}/brands/status/change/"+id,
          data: {status:1},
          success:function(result){
              $('td').find('[data-rel="'+id+'"]').removeClass('approval');
              $('td').find('[data-rel="'+id+'"]').find('strong').text('Approved');   
          }
      });
  }
</script>
<?php } ?>
@endsection

