@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="row justify-content-around">
                            <div class="col-6">
                                <div style="background-color: #fff">
                                    <div class="brandSelectionLeft">
                                        <div class="">
                                            <h5 class="">Check for the Brand you want to sell</h5>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label></label>
                                                <input class="form-control" type="text" placeholder="{{ __('Brand name') }}" name="brand_name" value="" required autofocus>
                                            </div>
                                            <div class="col-3 mt-3">
                                                <span class="btn btn-block btn-danger checkBrand">Check Brand</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            @if($userBrand)
                                            <div class="ml-3 mb-2"><strong>Your Latest Brands</strong></div>
                                            <ul style="padding-left: 15px;">
                                                @foreach($userBrand as $key=>$value)
                                                    <li data-rel="<?= $value->brand_id;?>" data-slug="<?= $value->brand_name?>" class="btn btn-danger oldBrands"><?= $value->brand_name;?></li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background-color: #fff">
                                    <div class="brandSelectionRight">
                                        <div class="brandCheck">
                                            <i class="cil-check-circle"></i>
                                        </div>
                                        <div class="brandName"></div>
                                        <div class="brandHint">
                                            <i class="cil-info"></i>
                                            <span>Your brand is approved as an open brand which means other sellers will be allowed to latch on to your listings. If you wish to gate the brand please do reach out to seller support for assistance.</span>
                                        </div>
                                        <div class="brandCreateBtn" style="display: none;">
                                            <a href="javascript:void(0)" class="btn btn-primary"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection


@section('javascript')
<script type="text/javascript">
    $(document).on('click', '.checkBrand', function(){
        $('.brandCreateBtn a').removeClass('brandApproval');
        $('.brandCreateBtn a').removeClass('createListing');

        var userid = '<?= Auth::user()->id?>';
        var brand = $('[name="brand_name"]').val();
        
        checkingBrand(brand, userid);
    });
    $(document).on('click', '.oldBrands', function(){
        $('.brandCreateBtn a').removeClass('brandApproval');
        $('.brandCreateBtn a').removeClass('createListing');

        var userid = '<?= Auth::user()->id?>';
        var brand = $(this).attr('data-slug');

        $('[name="brand_name"]').val(brand);
        
        checkingBrand(brand, userid);
    });

    function checkingBrand(brand, userid) {
        $.ajax({
            headers: {'x-csrf-token': '{{ csrf_token() }}' },
            method: 'post',
            url: "{{ url('') }}/brand/check/"+brand,
            data: {'user_id':userid},
            success:function(result){
                $('.brandName').html(brand);
                $('.brandCreateBtn').show();
                if(result) {
                    //console.log(result);
                    if(result.seller_id == userid) {
                        $('.brandCreateBtn').find('a').addClass('createListing');
                        $('.brandCreateBtn').find('a').text('Create New Listing');    
                    } else {
                        $('.brandCreateBtn').find('a').addClass('brandApproval');
                        $('.brandCreateBtn').find('a').text('Brand for Approval');
                        //var brandApprovalUrl = '{{ url('') }}/brand/approval';
                        //$('.brandCreateBtn').find('a').attr('href',brandApprovalUrl);    
                    }
                } else {
                    $('.brandCreateBtn').find('a').addClass('createListing');
                    $('.brandCreateBtn').find('a').text('Create New Listing');
                }
            }
        });
    }

    $(document).on('click', '.createListing', function(){
        var userid = '<?= Auth::user()->id?>';
        var brand = $('[name="brand_name"]').val();

        $.ajax({
            headers: {'x-csrf-token': '{{ csrf_token() }}' },
            method: 'post',
            url: "{{ url('') }}/brand/create",
            data: {'seller_id':userid, 'brand_name':brand, 'vertical':'<?=$vertical?>'},
            success:function(result){
                if(result) {
                    window.location.href = '{{ url('') }}/my/listing/create?vertical=<?=$vertical?>&brand='+brand+'&id='+result;
                }
            }
        }); 
    });

    $(document).on('click', '.brandApproval', function(){
        var brand = $('[name="brand_name"]').val();
        window.location.href = '{{ url('') }}/brand/approval?brand='+brand;
    });
</script>
@endsection