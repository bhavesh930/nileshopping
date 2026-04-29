@extends('dashboard.base')
<style type="text/css">
    .addAttributes { display: none !important; }
    .attibutesSection .row:last-child .addAttributes { display: block !important; }
</style>
@section('content')

        <div class="container-fluid">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="card">
                            <div class="card-header">
                              <i class="fa fa-align-justify"></i> {{ __('Category Attributes') }}
                            </div>
                            <form method="POST" action="{{ isset($editData) ? route('question.update', $id) : route('categoryAttibutesStore') }}">
                                @csrf
                                <input type="hidden" name="category_id" value="{{ $category_id }}" />
                                <div class="card-body attibutesSection">
                                    @if(isset($attribute_data) && !empty($attribute_data))
                                        @foreach($attribute_data as $key => $value)
                                            <div class="form-group row attrAddSection">
                                                <div class="col-sm-6">
                                                    <label>Title</label>
                                                    <input class="form-control" type="text" placeholder="{{ __('Title') }}" name="title[]" value="{{ $value->title }}" required autofocus>
                                                </div>
                                                <div class="col-1 mt-4">
                                                    <span class="addAttributes c-icon c-icon-2xl mt-1 mb-1 cil-plus" style="cursor: pointer; display: none;"></span>    
                                                </div>
                                                <div class="col-1 mt-4">
                                                    <span class="removeAttributes c-icon c-icon-2xl mt-1 mb-1 cil-x-circle" style="cursor: pointer;" data-rel="{{ $value->id }}"></span>
                                                </div>
                                            </div>    
                                        @endforeach
                                    @endif 
                                    <div class="form-group row attrAddSection">
                                        <div class="col-sm-6">
                                            <label>Title</label>
                                            <input class="form-control" type="text" placeholder="{{ __('Title') }}" name="title[]" value="" required autofocus>
                                        </div>
                                        <div class="col-1 mt-4">
                                            <span class="addAttributes c-icon c-icon-2xl mt-1 mb-1 cil-plus" style="cursor: pointer;"></span>    
                                        </div>
                                        <div class="col-1 mt-4">
                                            <span class="removeAttributes c-icon c-icon-2xl mt-1 mb-1 cil-x-circle" style="cursor: pointer;"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mt-5">
                                    <div class="col-2">
                                        <button class="btn btn-block btn-success" type="submit">{{ __('Save') }}</button>
                                    </div>
                                    <div class="col-2">
                                        <a href="{{ route('category.index') }}" class="btn btn-block btn-primary">{{ __('Return') }}</a> 
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection


@section('javascript')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    $(document).on('click', '.addAttributes', function(){
        var attributes = $('.attibutesSection .attrAddSection:first').clone();
        attributes.find('input[type=text]').val('');
        
        $('.attibutesSection').append(attributes);
    });

    $(document).on('click', '.removeAttributes', function(){
        var id = $(this).attr("data-rel");
        
        if($(this).parents('.attibutesSection').find('.attrAddSection').length > 1){
            if(typeof id !='undefined'){
                var confirm_action = confirm("Are you sure to delete!");
                if (confirm_action == true) {
                    $(this).parents('.attrAddSection').remove();

                    //removeOptionFromDatabase(id);
                }else{
                    return false;
                }
            }else{
                $(this).parents('.attrAddSection').remove();
            }
        }
    });
</script>
@endsection

