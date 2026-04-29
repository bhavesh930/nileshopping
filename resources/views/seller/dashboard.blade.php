@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="row justify-content-around">
                            <div class="col-3">
                                <div class="categoryListing">
                                    <ul class="categoryUl parentCategory">
                                @if($parent_category)
                                    @foreach($parent_category as $key=>$value)
                                        <li data-rel="<?= $value->id;?>" data-slug="<?= $value->slug?>"><?= $value->name;?></li>
                                    @endforeach
                                @endif    
                                    </ul>
                                    
                                </div>
                            </div>
                            <div class="col-3">
                                <div style="background-color: #fff">
                                    <div class="categoryListing">
                                        <ul class="categoryUl childCategory"></ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div style="background-color: #fff">
                                    <div class="categoryListing">
                                        <ul class="categoryUl subChildCategory"></ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div style="background-color: #fff">
                                    <div class="branding">
                                        <div class="brandCheck">
                                            <i class="cil-check-circle"></i>
                                        </div>
                                        <div class="brandBtn">
                                            <a href="javascript:void(0)" class="btn btn-primary">Select Brand</a>
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
    $(document).on('click', '.parentCategory li', function(){
        $('.parentCategory li').removeClass('active');
        $('.subChildCategory').empty();
        $('.branding').hide();
        $(this).addClass('active');
        var parent_id = $(this).attr('data-rel');
        getChildCategory(parent_id, 'child');

    });

    $(document).on('click', '.childCategory li', function(){
        $('.childCategory li').removeClass('active');
        $('.branding').hide();
        $(this).addClass('active');
        var parent_id = $(this).attr('data-rel');
        getChildCategory(parent_id, 'subChild');
    });

    $(document).on('click', '.subChildCategory li', function(){
        $('.subChildCategory li').removeClass('active');
        $('.branding').show();
        $(this).addClass('active');
        var slug = $(this).attr('data-slug');
        var url = "{{ url('') }}/addListings/single?vertical="+slug;
        $('.brandBtn').find('a').attr('href', url);
    });

    function getChildCategory(parent_id, type) {
        $.ajax({
            //headers: {'x-csrf-token': '{{ csrf_token() }}' },
            method: 'get',
            url: "{{ url('') }}/category/subList/"+parent_id,
            type: "JSON",
            //data:{position:data},
            success:function(result){
                console.log(result);
                if(type == 'child'){
                    $('.childCategory').empty();    
                }

                if(type == 'subChild'){
                    $('.subChildCategory').empty();    
                }
                
                $.each(result, function(key, item) {
                    var list = '<li data-rel="'+item.id+'" data-slug="'+item.slug+'">'+item.name+'</li>';
                    if(type == 'child'){
                        $(list).appendTo('.childCategory');    
                    }

                    if(type == 'subChild'){
                        $(list).appendTo('.subChildCategory');    
                    }
                    
                });
            }
        });
    }
</script>
@endsection