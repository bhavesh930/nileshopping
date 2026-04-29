@extends('dashboard.base')
<style type="text/css">
    #imagesPreview { object-fit: contain; max-width: 280px; height: 280px; }
    #preview_1, #preview_2, #preview_3 { object-fit: contain; max-width: 100px; height: 100px; }
</style>
@section('content')

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-align-justify"></i> {{ __('Brand Approval') }}
                        </div>
                        <div class="card-body">
                            <div class="row"> 
                            </div>
                            <br>

                            <form class="form-horizontal" action="{{ route('brandApprovalStore') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                @if(isset($listingData))
                                    @method('PUT')
                                @endif

                                <input type="hidden" name="brand_name" value="<?=$brand?>">
                                <div class="">
                                    <div class="fade-in">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-header"><strong>Brand Approval</strong></div>
                                                    <div class="card-body">
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label" for="text-input">Brand Name</label>
                                                            <div class="col-md-9">
                                                                <input class="form-control" id="text-input" type="text" name="brand_name" value="<?=isset($brand) ? $brand : ''?>" placeholder="Brand Name" disabled><!-- <span class="help-block">This is a help text</span> -->
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label" for="email-input">Is this Brand wisely distributed in offline market? </label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" id="select1" name="offline_market">
                                                                    <option value="">Select </option>
                                                                    <option value="1">Yes</option> 
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label" for="data-input">Brand Logo</label>
                                                            <div class="col-md-9">
                                                                <input class="form-control triggerChange" id="1" type="file" name="brand_logo" value="" required="" placeholder="Brand Logo">
                                                                <img id="preview_1" class="image_1" src="" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label" for="data-input">Brand Website Link</label>
                                                            <div class="col-md-9">
                                                                <input class="form-control" id="website_link" type="text" name="website" value="" placeholder="Website Link">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label" for="textarea-input">Where do you sell the products of this brand currently?</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" id="select1" name="sell_product">
                                                                    <option value="">Select One</option>
                                                                    <option value="Wholesale Distribution">Wholesale Distribution</option>
                                                                    <option value="Other Online Marketplace">Other Online Marketplace</option>
                                                                    <option value="Brand Retail Website">Brand Retail Website</option>
                                                                    <option value="Not Applicable">Not Applicable</option>
                                                                    <option value="Brick and Mortar Shop">Brick and Mortar Shop</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label" for="textarea-input">Sample MRP Tag images</label>
                                                            <div class="col-md-9">
                                                                <input class="form-control triggerChange" id="2" type="file" name="mrp_tag" value="" required="" placeholder="MRP Tag"> 
                                                                <img id="preview_2" class="image_2" src="" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label" for="textarea-input">Are you the brand owner?</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control" id="select1" name="brand_owner">
                                                                    <option value="">Select </option>
                                                                    <option value="1">Yes</option> 
                                                                    <option value="2">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label" for="textarea-input">Kindly upload any of the following document :Trademark Certificate , Brand Authorization Letter ( with trademark number if any ) or Invoice Copy</label>
                                                            <div class="col-md-9">
                                                                <input class="form-control triggerChange" accept=".doc, .pdf, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, text/plain" id="3" type="file" name="trademark_doc" required="">
                                                                <!-- <img id="preview_3" class="image_3" src="" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" /> -->
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-3 col-form-label" for="select2">Select the document type</label>
                                                            <div class="col-md-9">
                                                                <select class="form-control form-control" id="document_type" name="document_type">
                                                                    <option value="">Select one</option>
                                                                    <option value="Trademark Certificate">Trademark Certificate</option>
                                                                    <option value="Brand Authorization Letter">Brand Authorization Letter</option>
                                                                    <option value="Invoice">Invoice</option>
                                                                    <option value="Other Document">Other Document</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.col-->
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                    <button class="btn btn-primary" type="submit">Save changes</button>
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

<script type="text/javascript">
    $('.triggerChange').on('change', function() {
        var id = $(this).attr('id');
        imagesPreview(this, '#preview_'+id);
    });
    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var reader = new FileReader();
            reader.onload = function(event) {
                //console.log(input.name);
                var className = input.name;
                //$($.parseHTML('<img width="70">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                $(placeToInsertImagePreview).attr('src', event.target.result);
                $('.'+className).attr('src', event.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    };
</script>
@endsection