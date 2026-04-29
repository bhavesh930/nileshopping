@extends('dashboard.base')

<style type="text/css">
    #imagesPreview { object-fit: contain; max-width: 280px; height: 280px; }
    #preview_1, #preview_2, #preview_3, #preview_4, #preview_5 { object-fit: contain; max-width: 100px; height: 100px; }
    .boxShadow { box-shadow: rgb(34 34 34 / 60%) 0px 0px 4px 0px; }
    .error { background-color: rgba(255, 97, 89, 0.3); padding: 0 5px; border-radius: 4px; }
    .positionAbsolute { position: fixed !important; background-color: rgba(0,0,0,0.7); bottom: 10px; padding: 20px; width: calc(80vw) !important; margin-left: 10px; color: #fff; }
    .qc { float: right; }
    .requiredField { border-color: red !important; }
    /*.regularSize { display: none; }*/
</style>
@section('content')
        
        <div class="container-fluid">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="row justify-content-around">
                            <div class="col-5">
                                <div style="background-color: #fff">
                                    <div class="brandSelectionLeft">
                                        <div class="">
                                            <h5 class="">Product Photos</h5>
                                            <a href="javascript:void(0);"><span class="editPhoto" style="float: right; margin-top: -30px;" data-toggle="modal" data-target="#productImageModal">Edit</span></a>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <label></label>
                                                <div class="col-12 col-sm-12 col-md-12 mt-5 mb-5" style="text-align: center; cursor: pointer;"><i class="c-icon c-icon-2xl mt-5 mb-5 cil-image-plus" data-toggle="modal" data-target="#productImageModal"></i>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="" style="background-color: #fff">
                                    <div class="brandSelectionRight">
                                        <div class="">
                                            <?php 
                                            $wcnt = 0;
                                            if(isset($listingData) && !$listingData->product_name) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->sku) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->mrp) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->selling_price) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->stock) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->package_weight) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->package_length) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->package_height) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->hsn) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->manufacturer_detail) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->packer_detail) { $wcnt = $wcnt+1; }
                                            if(isset($listingData) && !$listingData->importer_detail) { $wcnt = $wcnt+1; }
                                            ?>
                                            <i class="cil-check-circle" style="float: left; margin-top: 5px; margin-right: 5px;"></i>
                                            <h5>Price, Stock and Shipping Information </h5>
                                            <a href="javascript:void(0);"><span class="editBasicInfo" style="float: right; margin-top: -30px;" data-toggle="modal" data-target="#basicInfoModal">Edit</span></a>
                                        </div>
                                        <?php if($wcnt > 0):?>
                                        <div class="error"><?= $wcnt;?> Errors</div>
                                        <?php endif;?>
                                        <!-- <div class="brandName"></div> -->
                                        <div class="brandHint">
                                            <div class="row mt-4">
                                                <div class="col-md-6 mb-2">
                                                    <span>Seller SKU ID: <?=isset($listingData) ? $listingData->sku : ''?></span>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <span>Listing Status: <?= (isset($listingData) && $listingData->status == 1) ? 'Active' : 'Inactive' ?></span>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <span>MRP: <?=isset($listingData) ? $listingData->mrp : ''?></span>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <span>Your Selling Price: <?=isset($listingData) ? $listingData->selling_price : ''?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brandCreateBtn" style="display: none;">
                                            <a href="javascript:void(0)" class="btn btn-primary"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4" style="background-color: #fff">
                                    <div class="brandSelectionRight">
                                        <div class="">
                                            <i class="cil-check-circle" style="float: left; margin-top: 5px; margin-right: 5px;"></i>
                                            <h5>Product Description</h5>
                                            <a href="javascript:void(0);"><span class="editProdInfo" style="float: right; margin-top: -30px;" data-toggle="modal" data-target="#ProdInfoModal">Edit</span></a>
                                        </div>
                                        <?php 
                                        $pcnt = 0;
                                        if(isset($listingData) && !$listingData->modal_number) { $pcnt = $pcnt+1; }
                                        if(isset($listingData) && !$listingData->primary_material_type) { $pcnt = $pcnt+1; }
                                        if(isset($listingData) && !$listingData->size) { $pcnt = $pcnt+1; }
                                        if(isset($listingData) && !$listingData->color) { $pcnt = $pcnt+1; }
                                        if(isset($listingData) && !$listingData->suitable_for) { $pcnt = $pcnt+1; }
                                        if(isset($listingData) && !$listingData->primary_material) { $pcnt = $pcnt+1; }
                                        if(isset($listingData) && !$listingData->age_group) { $pcnt = $pcnt+1; }
                                        if(isset($listingData) && !$listingData->warranty_summary) { $pcnt = $pcnt+1; }
                                        if(isset($listingData) && !$listingData->covered_warranty) { $pcnt = $pcnt+1; }
                                        if(isset($listingData) && !$listingData->not_covered_warranty) { $pcnt = $pcnt+1; }
                                        ?>
                                        <?php if($pcnt > 0):?>
                                        <div class="error"><?= $pcnt;?> Errors</div>
                                        <?php endif;?>
                                        <div class="brandName"></div>
                                        <div class="brandHint">
                                            <div class="row mt-4">
                                                <div class="col-md-6 mb-2">
                                                    <span>Size: <?= (isset($listingData)) ? $listingData->size : ''?></span>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <span>Style code: <?= (isset($listingData)) ? $listingData->modal_number : ''?></span>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <span>Material: <?= (isset($listingData) && isset($listingData->primary_material)) ? $listingData->primary_material : ''?></span>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <span>Ideal for: <?=isset($listingData) ? $listingData->suitable_for : ''?></span>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <span>Color: <?=isset($listingData) ? $listingData->color : ''?></span>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <span></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="brandCreateBtn" style="display: none;">
                                            <a href="javascript:void(0)" class="btn btn-primary"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4" style="background-color: #fff">
                                    <div class="brandSelectionRight">
                                        <div class="">
                                            <i class="cil-check-circle" style="float: left; margin-top: 5px; margin-right: 5px;"></i>
                                            <h5>Additional Description</h5>
                                            <a href="javascript:void(0);"><span class="editAddInfo" style="float: right; margin-top: -30px;" data-toggle="modal" data-target="#AddInfoModal">Edit</span></a>
                                        </div>
                                        <?php 
                                        $acnt = $additionalTotalRequiredCount - $additionalRequiredCount;
                                        ?>
                                        <?php if(isset($additionalData) && !empty($additionalData->toArray()) && $acnt > 0):?>
                                        <div class="error"><?= $acnt;?> Errors</div>
                                        <?php endif;?>
                                        <div class="brandName"></div>
                                        <div class="brandHint">
                                            <div class="row mt-4">
                                            
                                            @if(isset($additionalQuesView) && $additionalQuesView)
                                                @foreach($additionalQuesView as $vkey=>$vValue)
                                                <div class="col-md-6 mb-2">
                                                    <span>{{$vValue->question}} : <?=$vValue->answer?></span>
                                                </div>
                                                @endforeach
                                            @endif
                                            </div>
                                        </div>
                                        <div class="brandCreateBtn" style="display: none;">
                                            <a href="javascript:void(0)" class="btn btn-primary"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $totalError = $wcnt + $pcnt + $acnt;
                    if(isset($listingData) && $totalError == 0):?>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 positionAbsolute">
                        <div>
                            <div data-rel="<?=$listing_id?>" class="qc btn btn-secondary <?= ($listing_status == 0) ? 'requestApproval' : '' ?>"><?= ($listing_status == 2) ? 'Admin Approval' : ($listing_status == 3 ? 'Active' : 'QC')?></div>
                        </div>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        </div>

        <!-- Basic Info Modal -->
        <div class="modal fade" id="basicInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Price, Stock and Shipping Information</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <form class="form-horizontal" action="{{ isset($listingData) ? route('updateListingData', $listingData->id) : route('storeListingData') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        @if(isset($listingData))
                            @method('PUT')
                        @endif

                        <input type="hidden" name="listing_id" value="<?=$listing_id;?>" />
                        <div class="modal-body">
                            <div class="fade-in">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header"><strong>Shelling Information</strong></div>
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="text-input">Seller SKU ID *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->sku ? 'requiredField' : '' }}" id="text-input" type="text" name="sku" value="<?=isset($listingData) ? $listingData->sku : ''?>" required="" placeholder="Seller SKU ID"><!-- <span class="help-block">This is a help text</span> -->
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="text-input">Product Name *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->product_name ? 'requiredField' : '' }}" id="text-input" type="text" name="product_name" value="<?=isset($listingData) ? $listingData->product_name : ''?>" required="" placeholder="Product Name">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="email-input">Listing Status *</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control" id="select1" name="status">
                                                            <option value="">Select One</option>
                                                            <option value="1" <?= (isset($listingData) && $listingData->status == 1) ? 'selected' : ''?>>Active</option> 
                                                            <option value="0" <?= (isset($listingData) && $listingData->status == 0) ? 'selected' : ''?>>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="data-input">MRP *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->mrp ? 'requiredField' : '' }}" id="data-input" type="text" name="mrp" value="<?=isset($listingData) ? $listingData->mrp : ''?>" required="" placeholder="MRP">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="data-input">Your selling price *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="selling_price" value="<?=isset($listingData) ? $listingData->selling_price : ''?>" placeholder="Your selling price">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Fullfilment By</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control" id="select1" name="fullfilment">
                                                            <option value="">Select One</option>
                                                            <option value="1" <?= (isset($listingData) && $listingData->fullfilment == 1) ? 'selected' : ''?>>Seller</option>
                                                            <option value="2" <?= (isset($listingData) && $listingData->fullfilment == 2) ? 'selected' : ''?>>Seller smart</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Procurement type</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control" id="select1" name="procurement_type">
                                                            <option value="">Select One</option>
                                                            <option value="1" <?= (isset($listingData) && $listingData->procurement_type == 1) ? 'selected' : ''?>>Instock</option>
                                                            <option value="2" <?= (isset($listingData) && $listingData->procurement_type == 2) ? 'selected' : ''?>>Express</option>
                                                            <option value="3" <?= (isset($listingData) && $listingData->procurement_type == 3) ? 'selected' : ''?>>Domestic Procurement</option>
                                                            <option value="4" <?= (isset($listingData) && $listingData->procurement_type == 4) ? 'selected' : ''?>>Made to Order</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Procurement SLA</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->procurement_sla ? 'requiredField' : '' }}" id="data-input" type="text" name="procurement_sla" value="<?=isset($listingData) ? $listingData->procurement_sla : ''?>" required="" placeholder="Procurement SLA"> DAY
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Stock *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="stock" value="<?=isset($listingData) ? $listingData->stock : ''?>" placeholder="Stock">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="select1">Shipping provider</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control" id="select1" name="shipping_provider">
                                                            <option value="">Select one</option>
                                                            <option value="1" <?= (isset($listingData) && $listingData->shipping_provider == 1) ? 'selected' : ''?>>Seller</option>
                                                            <option value="2" <?= (isset($listingData) && $listingData->shipping_provider == 2) ? 'selected' : ''?>>Flipkart</option>
                                                            <option value="3" <?= (isset($listingData) && $listingData->shipping_provider == 3) ? 'selected' : ''?>>Seller and Flipkart</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Local Delivery Charge</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->local_delivery_charge ? 'requiredField' : '' }}" id="data-input" type="text" name="local_delivery_charge" value="<?=isset($listingData) ? $listingData->local_delivery_charge : ''?>" required="" placeholder="Local Delivery Charge"> INR
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Zonal Delivery Charge</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->zonal_delivery_charge ? 'requiredField' : '' }}" id="data-input" type="text" name="zonal_delivery_charge" value="<?=isset($listingData) ? $listingData->zonal_delivery_charge : ''?>" required="" placeholder="Zonal Delivery Charge"> INR
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">National Delivery Charge</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->national_delivery_charge ? 'requiredField' : '' }}" id="data-input" type="text" name="national_delivery_charge" value="<?=isset($listingData) ? $listingData->national_delivery_charge : ''?>" required="" placeholder="National Delivery Charge"> INR
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Package Weight *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="package_weight" value="<?=isset($listingData) ? $listingData->package_weight : ''?>" placeholder="Package Weight"> KG
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Package Length *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="package_length" value="<?=isset($listingData) ? $listingData->package_length : ''?>"  placeholder="Package Length"> CM
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Package Breadth *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="package_breadth" value="<?=isset($listingData) ? $listingData->package_breadth : ''?>"  placeholder="Package Breadth"> CM
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Package Height *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="package_height" value="<?=isset($listingData) ? $listingData->package_height : ''?>"  placeholder="Package Height"> CM
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="text-input">HSN *</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->hsn ? 'requiredField' : '' }}" id="text-input" type="text" name="hsn" value="<?=isset($listingData) ? $listingData->hsn : ''?>" required="" placeholder="HSN"><a href="javascript:void(0);"><span class="help-block">Find relevant HSN code</span></a>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Luxury Cess</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->luxury_cess ? 'requiredField' : '' }}" id="data-input" type="text" name="luxury_cess" value="<?=isset($listingData) ? $listingData->luxury_cess : ''?>" required="" placeholder="Luxury Cess">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="select2">Tax Code</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control form-control" id="select1" name="tax_code">
                                                            <option value="">Select one</option>
                                                            <option value="1" <?= (isset($listingData) && $listingData->tax_code == 1) ? 'selected' : ''?>>GST_0</option>
                                                            <option value="2" <?= (isset($listingData) && $listingData->tax_code == 2) ? 'selected' : ''?>>GST_3</option>
                                                            <option value="3" <?= (isset($listingData) && $listingData->tax_code == 3) ? 'selected' : ''?>>GST_5</option>
                                                            <option value="4" <?= (isset($listingData) && $listingData->tax_code == 4) ? 'selected' : ''?>>GST_12</option>
                                                            <option value="5" <?= (isset($listingData) && $listingData->tax_code == 5) ? 'selected' : ''?>>GST_18</option>
                                                            <option value="6" <?= (isset($listingData) && $listingData->tax_code == 6) ? 'selected' : ''?>>GST_28</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="select2">Country Origin <i class="cil-info" data-toggle="tooltip" data-placement="top" title="As per regulatory guidelines, please provide country of origin for the product you wish to sell"></i></label>
                                                    <div class="col-md-9">
                                                        <select class="form-control form-control" id="select1" name="country_origin">
                                                            <option value="">Select one</option>
                                                            <option value="1" <?= (isset($listingData) && $listingData->country_origin == 1) ? 'selected' : ''?>>India</option>
                                                        </select>
                                                        <span class="help-block">As per regulatory guidelines, please provide country of origin for the product you wish to sell</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Manufacturer Details * <i class="cil-info" data-toggle="tooltip" data-placement="top" title="You need to provide manufacturer_details as per the Legal Metrology rules. You cannot list your product without providing this detail"></i></label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="manufacturer_detail" value="<?=isset($listingData) ? $listingData->manufacturer_detail : ''?>" placeholder="Manufacturer Detail"><span class="help-block">You need to provide manufacturer_details as per the Legal Metrology rules. You cannot list your product without providing this detail</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Packer Details * <i class="cil-info" data-toggle="tooltip" data-placement="top" title="You need to provide packer_details as per the Legal Metrology rules. You cannot list your product without providing this detail"></i></label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="packer_detail" value="<?=isset($listingData) ? $listingData->packer_detail : ''?>"  placeholder="Packer Detail"><span class="help-block">You need to provide packer_details as per the Legal Metrology rules. You cannot list your product without providing this detail</span>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Importer Details * <i class="cil-info" data-toggle="tooltip" data-placement="top" title="You need to provide importer_details as per the Legal Metrology rules. You cannot list your product without providing this detail"></i></label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="importer_detail" value="<?=isset($listingData) ? $listingData->importer_detail : ''?>"  placeholder="Importer Detail"><span class="help-block">You need to provide importer_details as per the Legal Metrology rules. You cannot list your product without providing this detail</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.col-->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            <button class="btn btn-primary" type="submit">Save changes</button>
                        </div>
                    </form>
                </div>
            <!-- /.modal-content-->
            </div>
        <!-- /.modal-dialog-->
        </div>
        <!-- End Basic Info Modal -->

        <!-- Product Info Modal -->
        <div class="modal fade" id="ProdInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Product Description</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <form class="form-horizontal" action="{{ isset($listingData) ? route('updateListingData', $listingData->id) : route('storeListingData') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        @if(isset($listingData))
                        @method('PUT')
                        @endif

                        <input type="hidden" name="listing_id" value="<?=$listing_id;?>" />
                        <div class="modal-body">
                            <div class="fade-in">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="text-input">Modal Number</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->modal_number ? 'requiredField' : '' }}" id="text-input" type="text" name="modal_number" value="<?=isset($listingData) ? $listingData->modal_number : ''?>" required="" placeholder="Modal Number"><!-- <span class="help-block">This is a help text</span> -->
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="text-input">Brand Color</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->brand_color ? 'requiredField' : '' }}" id="text-input" type="text" name="brand_color" value="<?=isset($listingData) ? $listingData->brand_color : ''?>" required="" placeholder="Brand Color">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="email-input">Primary Material Subtype</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control {{ isset($listingData) && !$listingData->primary_material_type ? 'requiredField' : '' }}" id="select1" name="primary_material_type" required="">
                                                            <option>Select One</option>
                                                            <option value="Acacia (Kasia)" <?= (isset($listingData) && $listingData->primary_material_type == "Acacia (Kasia)") ? 'selected' : ''?>>Acacia (Kasia)</option>
                                                            <option value="Acrylic" <?= (isset($listingData) && $listingData->primary_material_type == "Acrylic") ? 'selected' : ''?>>Acrylic</option>
                                                            <option value="Aluminium" <?= (isset($listingData) && $listingData->primary_material_type == "Aluminium") ? 'selected' : ''?>>Aluminium</option>
                                                            <option value="Ash (Mohin)" <?= (isset($listingData) && $listingData->primary_material_type == "Ash (Mohin)") ? 'selected' : ''?>>Ash (Mohin)</option>
                                                            <option value="Babul" <?= (isset($listingData) && $listingData->primary_material_type == "Babul") ? 'selected' : ''?>>Babul</option>
                                                            <option value="Balsa" <?= (isset($listingData) && $listingData->primary_material_type == "Balsa") ? 'selected' : ''?>>Balsa</option>
                                                            <option value="Bamboo" <?= (isset($listingData) && $listingData->primary_material_type == "Bamboo") ? 'selected' : ''?>>Bamboo</option>
                                                            <option value="Beech" <?= (isset($listingData) && $listingData->primary_material_type == "Beech") ? 'selected' : ''?>>Beech</option>
                                                            <option value="Brass" <?= (isset($listingData) && $listingData->primary_material_type == "Brass") ? 'selected' : ''?>>Brass</option>
                                                            <option value="Bronze" <?= (isset($listingData) && $listingData->primary_material_type == "Bronze") ? 'selected' : ''?>>Bronze</option>
                                                            <option value="Cane" <?= (isset($listingData) && $listingData->primary_material_type == "Cane") ? 'selected' : ''?>>Cane</option>
                                                            <option value="Carbon Steel" <?= (isset($listingData) && $listingData->primary_material_type == "Carbon Steel") ? 'selected' : ''?>>Carbon Steel</option>
                                                            <option value="Cast Iron" <?= (isset($listingData) && $listingData->primary_material_type == "Cast Iron") ? 'selected' : ''?>>Cast Iron</option>
                                                            <option value="Cedar Pine (Devdar)" <?= (isset($listingData) && $listingData->primary_material_type == "Cedar Pine (Devdar)") ? 'selected' : ''?>>Cedar Pine (Devdar)</option>
                                                            <option value="Chenille" <?= (isset($listingData) && $listingData->primary_material_type == "Chenille") ? 'selected' : ''?>>Chenille</option>
                                                            <option value="Cherry Hardwood" <?= (isset($listingData) && $listingData->primary_material_type == "Cherry Hardwood") ? 'selected' : ''?>>Cherry Hardwood</option>
                                                            <option value="Chestnut" <?= (isset($listingData) && $listingData->primary_material_type == "Chestnut") ? 'selected' : ''?>>Chestnut</option>
                                                            <option value="Copper" <?= (isset($listingData) && $listingData->primary_material_type == "Copper") ? 'selected' : ''?>>Copper</option>
                                                            <option value="Cotton" <?= (isset($listingData) && $listingData->primary_material_type == "Cotton") ? 'selected' : ''?>>Cotton</option>
                                                            <option value="Distressed Leather" <?= (isset($listingData) && $listingData->primary_material_type == "Distressed Leather") ? 'selected' : ''?>>Distressed Leather</option>
                                                            <option value="Ebony" <?= (isset($listingData) && $listingData->primary_material_type == "Ebony") ? 'selected' : ''?>>Ebony</option>
                                                            <option value="Elephant Skin" <?= (isset($listingData) && $listingData->primary_material_type == "Elephant Skin") ? 'selected' : ''?>>Elephant Skin</option>
                                                            <option value="Fiber Glass" <?= (isset($listingData) && $listingData->primary_material_type == "Fiber Glass") ? 'selected' : ''?>>Fiber Glass</option>
                                                            <option value="Fir (Tos)" <?= (isset($listingData) && $listingData->primary_material_type == "Fir (Tos)") ? 'selected' : ''?>>Fir (Tos)</option>
                                                            <option value="Foam" <?= (isset($listingData) && $listingData->primary_material_type == "Foam") ? 'selected' : ''?>>Foam</option>
                                                            <option value="Granite" <?= (isset($listingData) && $listingData->primary_material_type == "Granite") ? 'selected' : ''?>>Granite</option>
                                                            <option value="HDF" <?= (isset($listingData) && $listingData->primary_material_type == "HDF") ? 'selected' : ''?>>HDF</option>
                                                            <option value="Hemp" <?= (isset($listingData) && $listingData->primary_material_type == "Hemp") ? 'selected' : ''?>>Hemp</option>
                                                            <option value="High Density Block Board" <?= (isset($listingData) && $listingData->primary_material_type == "High Density Block Board") ? 'selected' : ''?>>High Density Block Board</option>
                                                            <option value="Jute" <?= (isset($listingData) && $listingData->primary_material_type == "Jute") ? 'selected' : ''?>>Jute</option>
                                                            <option value="Leatherette" <?= (isset($listingData) && $listingData->primary_material_type == "Leatherette") ? 'selected' : ''?>>Leatherette</option>
                                                            <option value="Linen" <?= (isset($listingData) && $listingData->primary_material_type == "Linen") ? 'selected' : ''?>>Linen</option>
                                                            <option value="MDF" <?= (isset($listingData) && $listingData->primary_material_type == "MDF") ? 'selected' : ''?>>MDF</option>
                                                            <option value="Mahogany" <?= (isset($listingData) && $listingData->primary_material_type == "Mahogany") ? 'selected' : ''?>>Mahogany</option>
                                                            <option value="Mango Wood" <?= (isset($listingData) && $listingData->primary_material_type == "Mango Wood") ? 'selected' : ''?>>Mango Wood</option>
                                                            <option value="Marble" <?= (isset($listingData) && $listingData->primary_material_type == "Marble") ? 'selected' : ''?>>Marble</option>
                                                            <option value="Micro Fiber" <?= (isset($listingData) && $listingData->primary_material_type == "Micro Fiber") ? 'selected' : ''?>>Micro Fiber</option>
                                                            <option value="Micro Suede" <?= (isset($listingData) && $listingData->primary_material_type == "Micro Suede") ? 'selected' : ''?>>Micro Suede</option>
                                                            <option value="Mosaic" <?= (isset($listingData) && $listingData->primary_material_type == "Mosaic") ? 'selected' : ''?>>Mosaic</option>
                                                            <option value="Oak &amp; Birch (Batula)" <?= (isset($listingData) && $listingData->primary_material_type == "Oak &amp; Birch (Batula)") ? 'selected' : ''?>>Oak &amp; Birch (Batula)</option>
                                                            <option value="Olefin" <?= (isset($listingData) && $listingData->primary_material_type == "Olefin") ? 'selected' : ''?>>Olefin</option>
                                                            <option value="PC" <?= (isset($listingData) && $listingData->primary_material_type == "PC") ? 'selected' : ''?>>PC</option>
                                                            <option value="PP" <?= (isset($listingData) && $listingData->primary_material_type == "PP") ? 'selected' : ''?>>PP</option>
                                                            <option value="PU Leatherette" <?= (isset($listingData) && $listingData->primary_material_type == "PU Leatherette") ? 'selected' : ''?>>PU Leatherette</option>
                                                            <option value="PVC" <?= (isset($listingData) && $listingData->primary_material_type == "PVC") ? 'selected' : ''?>>PVC</option>
                                                            <option value="Particle Board" <?= (isset($listingData) && $listingData->primary_material_type == "Particle Board") ? 'selected' : ''?>>Particle Board</option>
                                                            <option value="Plywood" <?= (isset($listingData) && $listingData->primary_material_type == "Plywood") ? 'selected' : ''?>>Plywood</option>
                                                            <option value="Polyester" <?= (isset($listingData) && $listingData->primary_material_type == "Polyester") ? 'selected' : ''?>>Polyester</option>
                                                            <option value="Poplar" <?= (isset($listingData) && $listingData->primary_material_type == "Poplar") ? 'selected' : ''?>>Poplar</option>
                                                            <option value="Quartz" <?= (isset($listingData) && $listingData->primary_material_type == "Quartz") ? 'selected' : ''?>>Quartz</option>
                                                            <option value="Rattan" <?= (isset($listingData) && $listingData->primary_material_type == "Rattan") ? 'selected' : ''?>>Rattan</option>
                                                            <option value="Rayon" <?= (isset($listingData) && $listingData->primary_material_type == "Rayon") ? 'selected' : ''?>>Rayon</option>
                                                            <option value="Rosewood (Sheesham)" <?= (isset($listingData) && $listingData->primary_material_type == "Rosewood (Sheesham)") ? 'selected' : ''?>>Rosewood (Sheesham)</option>
                                                            <option value="Rubber Wood" <?= (isset($listingData) && $listingData->primary_material_type == "Rubber Wood") ? 'selected' : ''?>>Rubber Wood</option>
                                                            <option value="Sandstone" <?= (isset($listingData) && $listingData->primary_material_type == "Sandstone") ? 'selected' : ''?>>Sandstone</option>
                                                            <option value="Saran" <?= (isset($listingData) && $listingData->primary_material_type == "Saran") ? 'selected' : ''?>>Saran</option>
                                                            <option value="Satinwood (Bhirra)" <?= (isset($listingData) && $listingData->primary_material_type == "Satinwood (Bhirra)") ? 'selected' : ''?>>Satinwood (Bhirra)</option>
                                                            <option value="Semi Aniline Leather" <?= (isset($listingData) && $listingData->primary_material_type == "Semi Aniline Leather") ? 'selected' : ''?>>Semi Aniline Leather</option>
                                                            <option value="Silk" <?= (isset($listingData) && $listingData->primary_material_type == "Silk") ? 'selected' : ''?>>Silk</option>
                                                            <option value="Split Leather" <?= (isset($listingData) && $listingData->primary_material_type == "Split Leather") ? 'selected' : ''?>>Split Leather</option>
                                                            <option value="Stainless Steel" <?= (isset($listingData) && $listingData->primary_material_type == "Stainless Steel") ? 'selected' : ''?>>Stainless Steel</option>
                                                            <option value="Suede Leather" <?= (isset($listingData) && $listingData->primary_material_type == "Suede Leather") ? 'selected' : ''?>>Suede Leather</option>
                                                            <option value="Sycamore" <?= (isset($listingData) && $listingData->primary_material_type == "Sycamore") ? 'selected' : ''?>>Sycamore</option>
                                                            <option value="Teak (Sagun)" <?= (isset($listingData) && $listingData->primary_material_type == "Teak (Sagun)") ? 'selected' : ''?>>Teak (Sagun)</option>
                                                            <option value="Tempered Glass" <?= (isset($listingData) && $listingData->primary_material_type == "Tempered Glass") ? 'selected' : ''?>>Tempered Glass</option>
                                                            <option value="Top Grain Leather" <?= (isset($listingData) && $listingData->primary_material_type == "Top Grain Leather") ? 'selected' : ''?>>Top Grain Leather</option>
                                                            <option value="Toughened Glass" <?= (isset($listingData) && $listingData->primary_material_type == "Toughened Glass") ? 'selected' : ''?>>Toughened Glass</option>
                                                            <option value="Velour" <?= (isset($listingData) && $listingData->primary_material_type == "Velour") ? 'selected' : ''?>>Velour</option>
                                                            <option value="Velvet" <?= (isset($listingData) && $listingData->primary_material_type == "Velvet") ? 'selected' : ''?>>Velvet</option>
                                                            <option value="Veneer" <?= (isset($listingData) && $listingData->primary_material_type == "Veneer") ? 'selected' : ''?>>Veneer</option>
                                                            <option value="Vinyl" <?= (isset($listingData) && $listingData->primary_material_type == "Vinyl") ? 'selected' : ''?>>Vinyl</option>
                                                            <option value="Walnut" <?= (isset($listingData) && $listingData->primary_material_type == "Walnut") ? 'selected' : ''?>>Walnut</option>
                                                            <option value="Wicker" <?= (isset($listingData) && $listingData->primary_material_type == "Wicker") ? 'selected' : ''?>>Wicker</option>
                                                            <option value="Wrought Iron" <?= (isset($listingData) && $listingData->primary_material_type == "Wrought Iron") ? 'selected' : ''?>>Wrought Iron</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="email-input">Size</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control {{ isset($listingData) && !$listingData->size ? 'requiredField' : '' }}" id="select1" name="size" required="">
                                                            <option>Select One</option>
                                                            <option value="S" <?= (isset($listingData) && $listingData->size == 'S') ? 'selected' : ''?>>S</option>
                                                            <option value="M" <?= (isset($listingData) && $listingData->size == 'M') ? 'selected' : ''?>>M</option>
                                                            <option value="L" <?= (isset($listingData) && $listingData->size == 'L') ? 'selected' : ''?>>L</option>
                                                            <option value="XL" <?= (isset($listingData) && $listingData->size == 'XL') ? 'selected' : ''?>>XL</option>
                                                            <option value="XXL" <?= (isset($listingData) && $listingData->size == 'XXL') ? 'selected' : ''?>>XXL</option>
                                                            <option value="XXXL" <?= (isset($listingData) && $listingData->size == 'XXXL') ? 'selected' : ''?>>XXXL</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="email-input">Color</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control {{ isset($listingData) && !$listingData->color ? 'requiredField' : '' }}" id="select1" name="color" required="">
                                                            <option>Select One</option>
                                                            <option value="Beige" <?= (isset($listingData) && $listingData->color == 'Beige') ? 'selected' : ''?>>Beige</option>
                                                            <option value="Black" <?= (isset($listingData) && $listingData->color == 'Black') ? 'selected' : ''?>>Black</option>
                                                            <option value="Blue" <?= (isset($listingData) && $listingData->color == 'Blue') ? 'selected' : ''?>>Blue</option>
                                                            <option value="Brown" <?= (isset($listingData) && $listingData->color == 'Brown') ? 'selected' : ''?>>Brown</option>
                                                            <option value="Gold" <?= (isset($listingData) && $listingData->color == 'Gold') ? 'selected' : ''?>>Gold</option>
                                                            <option value="Green" <?= (isset($listingData) && $listingData->color == 'Green') ? 'selected' : ''?>>Green</option>
                                                            <option value="Grey" <?= (isset($listingData) && $listingData->color == 'Grey') ? 'selected' : ''?>>Grey</option>
                                                            <option value="Maroon" <?= (isset($listingData) && $listingData->color == 'Maroon') ? 'selected' : ''?>>Maroon</option>
                                                            <option value="Multicolor" <?= (isset($listingData) && $listingData->color == 'Multicolor') ? 'selected' : ''?>>Multicolor</option>
                                                            <option value="Orange" <?= (isset($listingData) && $listingData->color == 'Orange') ? 'selected' : ''?>>Orange</option>
                                                            <option value="Pink" <?= (isset($listingData) && $listingData->color == 'Pink') ? 'selected' : ''?>>Pink</option>
                                                            <option value="Purple" <?= (isset($listingData) && $listingData->color == 'Purple') ? 'selected' : ''?>>Purple</option>
                                                            <option value="Red" <?= (isset($listingData) && $listingData->color == 'Red') ? 'selected' : ''?>>Red</option>
                                                            <option value="Silver" <?= (isset($listingData) && $listingData->color == 'Silver') ? 'selected' : ''?>>Silver</option>
                                                            <option value="White" <?= (isset($listingData) && $listingData->color == 'White') ? 'selected' : ''?>>White</option>
                                                            <option value="Yellow" <?= (isset($listingData) && $listingData->color == 'Yellow') ? 'selected' : ''?>>Yellow</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="data-input">Suitable For</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control {{ isset($listingData) && !$listingData->suitable_for ? 'requiredField' : '' }}" id="select1" name="suitable_for" required="">
                                                            <option value="">Select One</option>
                                                            <option value="Kids" <?= (isset($listingData) && $listingData->suitable_for == 'Kids') ? 'selected' : ''?>>Kids</option>
                                                            <option value="Mens" <?= (isset($listingData) && $listingData->suitable_for == 'Mens') ? 'selected' : ''?>>Mens</option>
                                                            <option value="Womens" <?= (isset($listingData) && $listingData->suitable_for == 'Womens') ? 'selected' : ''?>>Womens</option>
                                                            <option value="Adults" <?= (isset($listingData) && $listingData->suitable_for == 'Adults') ? 'selected' : ''?>>Adults</option>
                                                            <option value="All" <?= (isset($listingData) && $listingData->suitable_for == 'All') ? 'selected' : ''?>>All</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="data-input">Primary Material</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control" id="select1" name="primary_material">
                                                            <option value="">Select One</option>
                                                            <option value="Bamboo" <?= (isset($listingData) && $listingData->primary_material == 'Bamboo') ? 'selected' : ''?>>Bamboo</option>
                                                            <option value="Bonded Leather" <?= (isset($listingData) && $listingData->primary_material == 'Bonded Leather') ? 'selected' : ''?>>Bonded Leather</option>
                                                            <option value="Cane" <?= (isset($listingData) && $listingData->primary_material == "Cane") ? 'selected' : ''?>>Cane</option>
                                                            <option value="Ceramic" <?= (isset($listingData) && $listingData->primary_material == "Ceramic") ? 'selected' : ''?>>Ceramic</option>
                                                            <option value="Engineered Wood" <?= (isset($listingData) && $listingData->primary_material == "Engineered Wood") ? 'selected' : ''?>>Engineered Wood</option>
                                                            <option value="Fabric" <?= (isset($listingData) && $listingData->primary_material == "Fabric") ? 'selected' : ''?>>Fabric</option>
                                                            <option value="Foam" <?= (isset($listingData) && $listingData->primary_material == "Foam") ? 'selected' : ''?>>Foam</option>
                                                            <option value="Glass" <?= (isset($listingData) && $listingData->primary_material == "Glass") ? 'selected' : ''?>>Glass</option>
                                                            <option value="Half-leather" <?= (isset($listingData) && $listingData->primary_material == "Half-leather") ? 'selected' : ''?>>Half-leather</option>
                                                            <option value="Leather" <?= (isset($listingData) && $listingData->primary_material == "Leather") ? 'selected' : ''?>>Leather</option>
                                                            <option value="Leatherette" <?= (isset($listingData) && $listingData->primary_material == "Leatherette") ? 'selected' : ''?>>Leatherette</option>
                                                            <option value="Metal" <?= (isset($listingData) && $listingData->primary_material == "Metal") ? 'selected' : ''?>>Metal</option>
                                                            <option value="Natural Fiber" <?= (isset($listingData) && $listingData->primary_material == "Natural Fiber") ? 'selected' : ''?>>Natural Fiber</option>
                                                            <option value="Plastic" <?= (isset($listingData) && $listingData->primary_material == "Plastic") ? 'selected' : ''?>>Plastic</option>
                                                            <option value="Solid Wood" <?= (isset($listingData) && $listingData->primary_material == "Solid Wood") ? 'selected' : ''?>>Solid Wood</option>
                                                            <option value="Stone" <?= (isset($listingData) && $listingData->primary_material == "Stone") ? 'selected' : ''?>>Stone</option>
                                                            <option value="Synthetic Fiber" <?= (isset($listingData) && $listingData->primary_material == "Synthetic Fiber") ? 'selected' : ''?>>Synthetic Fiber</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="data-input">Delivery Condition</label>
                                                    <div class="col-md-9">
                                                        <select class="form-control {{ isset($listingData) && !$listingData->delivery_condition ? 'requiredField' : '' }}" id="select1" name="delivery_condition" required="">
                                                            <option value="">Select One</option>
                                                            <option value="Pre-assembled" <?= (isset($listingData) && $listingData->delivery_condition == "Pre-assembled") ? 'selected' : ''?>>Pre-assembled</option>
                                                            <option value="DIY(Do-It-Yourself)" <?= (isset($listingData) && $listingData->delivery_condition == "DIY(Do-It-Yourself)") ? 'selected' : ''?>>DIY(Do-It-Yourself)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Age Group</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->age_group ? 'requiredField' : '' }}" id="data-input" type="text" name="age_group" value="<?=isset($listingData) ? $listingData->age_group : ''?>" required="" placeholder="Age Group">
                                                    </div>
                                                </div>
                                                <!-- <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Width</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="product_width" value="<?=isset($listingData) ? $listingData->product_width : ''?>" required="" placeholder="Width"> mm
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Height</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="product_height" value="<?=isset($listingData) ? $listingData->product_height : ''?>" required="" placeholder="Height"> mm
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Depth</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control" id="data-input" type="text" name="product_depth" value="<?=isset($listingData) ? $listingData->product_depth : ''?>" required="" placeholder="depth"> mm
                                                    </div>
                                                </div> -->
                                                <?php 
                                                $productWt = '';
                                                $selectedPUnit = '';
                                                
                                                if(isset($listingData) && $listingData->product_weight ) {
                                                    $exp = explode(' ', $listingData->product_weight);
                                                    $productWt = $exp[0];
                                                    $selectedPUnit = $exp[1] ?? '';
                                                }
                                                ?>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Weight</label>
                                                    <div class="col-md-6">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->product_weight ? 'requiredField' : '' }}" id="data-input" type="text" name="product_weight" value="<?=isset($listingData) ? $productWt : ''?>" required="" placeholder="Weight">
                                                    </div>
                                                    <div>
                                                        <select class="form-control" id="select1" name="product_weight_unit">
                                                            <option value="Kg" <?= (isset($listingData) && $selectedPUnit == "Kg") ? 'selected' : ''?>>Kg</option>
                                                            <option value="Tone" <?= (isset($listingData) && $selectedPUnit == "Tone") ? 'selected' : ''?>>Tone</option>
                                                            <option value="lbs" <?= (isset($listingData) && $selectedPUnit == "lbs") ? 'selected' : ''?>>lbs</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Warranty Summary</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->warranty_summary ? 'requiredField' : '' }}" id="data-input" type="text" name="warranty_summary" value="<?=isset($listingData) ? $listingData->warranty_summary : ''?>" required="" placeholder="Warranty Summary">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Covered in Warranty</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->covered_warranty ? 'requiredField' : '' }}" id="data-input" type="text" name="covered_warranty" value="<?=isset($listingData) ? $listingData->covered_warranty : ''?>" required="" placeholder="Covered in Warranty">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label" for="textarea-input">Not Covered in Warranty</label>
                                                    <div class="col-md-9">
                                                        <input class="form-control {{ isset($listingData) && !$listingData->not_covered_warranty ? 'requiredField' : '' }}" id="data-input" type="text" name="not_covered_warranty" value="<?=isset($listingData) ? $listingData->not_covered_warranty : ''?>" required="" placeholder="Not Covered in Warranty">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.col-->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary" type="submit">Save changes</button>
                        </div>
                    </form>
                </div>
            <!-- /.modal-content-->
            </div>
        <!-- /.modal-dialog-->
        </div>
        <!-- End Basic Info Modal -->

        <!-- Additional Description Info Modal -->
        <div class="modal fade" id="AddInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Additional Description</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <form class="form-horizontal" action="{{ isset($additionalData) ? route('updateListingAddition', $listing_id) : route('storeListingAddition') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    @if(isset($additionalData))
                    @method('PUT')
                        <?php $addArr = array();?>
                        @foreach($additionalData as $addKey => $addValue)
                            <?php $addArr[$addValue->question_id] = $addValue->answer; ?>
                        @endforeach
                    @endif

                    <?php //echo '<pre>'; print_r($addArr); echo $addArr[11]; ?>

                    <input type="hidden" name="listing_id" value="<?=$listing_id;?>" />
                    <div class="modal-body">
                        <div class="fade-in">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            @if(isset($questioList))
                                                @foreach($questioList as $key=>$value)
                                                    <div class="card-header mb-5"><strong>{{$value['section']}}</strong></div>
                                                    @foreach(json_decode(json_encode($value['question'])) as $qkey => $question)
                                                    
                                                    @if($question->type == 'Dropdown' || $question->type == 'Radio' || $question->type == 'Checkbox')
                                                    @php
                                                    $questionOption = App\Models\QuestionOption::where('question_id', $question->id)->get();
                                                    @endphp
                                                    @endif

                                                    @php
                                                        $errorClass = '';
                                                    @endphp

                                                    @if(isset($additionalData) && !empty($additionalData->toArray()) && $acnt > 0 && $question->required == 1 && (!isset($addArr[$question->id]) || !$addArr[$question->id]))
                                                        @php
                                                        $errorClass = 'requiredField';
                                                        @endphp
                                                    @endif

                                                    @if($question->type == 'Text')
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label" for="text-input">{{$question->question}} @if($question->hint)<i class="cil-info" data-toggle="tooltip" data-placement="top" title="{{$question->hint}}"></i>@endif </label>
                                                        <div class="col-md-9">
                                                            <input class="form-control {{ isset($errorClass) ? $errorClass : ''}}" id="text-input" type="text" name="product[{{$question->category_id}}][{{$question->id}}]" placeholder="{{$question->placeholder}}" value="<?= (isset($additionalData) && isset($addArr[$question->id])) ? $addArr[$question->id] : ''; ?>"><!-- <span class="help-block">This is a help text</span> -->
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($question->type == 'Dropdown')
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label" for="email-input">{{$question->question}} @if($question->hint)<i class="cil-info" data-toggle="tooltip" data-placement="top" title="{{$question->hint}}"></i>@endif </label>
                                                        <div class="col-md-9">
                                                            <select class="form-control {{ isset($errorClass) ? $errorClass : ''}}" id="select1" name="product[{{$question->category_id}}][{{$question->id}}]">
                                                                <option value="">Select One</option>
                                                                @if($questionOption)
                                                                @foreach($questionOption as $option)
                                                                <option value="{{$option->option}}" <?= (isset($additionalData) && isset($addArr[$question->id]) && $addArr[$question->id] == $option->option) ? 'selected' : ''?>>{{$option->option}}</option>
                                                                @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($question->type == 'Radio')
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label">{{$question->question}} @if($question->hint)<i class="cil-info" data-toggle="tooltip" data-placement="top" title="{{$question->hint}}"></i>@endif </label>
                                                        <div class="col-md-9 col-form-label">
                                                            @if($questionOption)
                                                                @foreach($questionOption as $okey => $option)
                                                                <div class="form-check form-check-inline mr-1">
                                                                    <input class="form-check-input" id="inline-radio<?=$okey+1?>" type="radio" value="{{$option->option}}" name="product[{{$question->category_id}}][{{$question->id}}]" <?= (isset($additionalData) && isset($addArr[$question->id]) && $addArr[$question->id] == $option->option) ? 'checked' : ''?>>
                                                                    <label class="form-check-label" for="inline-radio<?=$okey+1?>">{{$option->option}}</label>
                                                                </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($question->type == 'Checkbox')
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label">{{$question->question}} @if($question->hint)<i class="cil-info" data-toggle="tooltip" data-placement="top" title="{{$question->hint}}"></i>@endif </label>
                                                        <div class="col-md-9 col-form-label">
                                                            @if($questionOption)
                                                                @foreach($questionOption as $ockey => $option)
                                                                <div class="form-check form-check-inline mr-1">
                                                                    <input class="form-check-input" id="inline-checkbox<?=$ockey+1?>" type="checkbox" name="product[{{$question->category_id}}][{{$question->id}}][]" value="{{$option->option}}" <?= (isset($additionalData) && isset($addArr[$question->id]) && $addArr[$question->id] == $option->option) ? 'checked' : ''?>>
                                                                    <label class="form-check-label" for="inline-checkbox<?=$ockey+1?>">{{$option->option}}</label>
                                                                </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($question->type == 'Textarea')
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label" for="textarea-input">{{$question->question}} @if($question->hint)<i class="cil-info" data-toggle="tooltip" data-placement="top" title="{{$question->hint}}"></i>@endif </label>
                                                        <div class="col-md-9">
                                                            <textarea class="form-control {{ isset($errorClass) ? $errorClass : ''}}" id="textarea-input" name="{{$question->category_id}}_{{$question->id}}" rows="9" placeholder="{{$question->placeholder}}"><?= (isset($additionalData) && isset($addArr[$question->id])) ? $addArr[$question->id] : ''; ?></textarea>
                                                        </div>
                                                    </div>    
                                                    @endif

                                                    @if($question->type == 'Number')
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label" for="text-input">{{$question->question}} @if($question->hint)<i class="cil-info" data-toggle="tooltip" data-placement="top" title="{{$question->hint}}"></i>@endif </label>
                                                        <div class="col-md-9">
                                                            <input class="form-control {{ isset($errorClass) ? $errorClass : ''}}" id="text-input" type="number" min="1" name="{{$question->category_id}}_{{$question->id}}" placeholder="{{$question->placeholder}}" value="<?= (isset($additionalData) && isset($addArr[$question->id])) ? $addArr[$question->id] : ''; ?>"><!-- <span class="help-block">This is a help text</span>-->
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($question->type == 'Size')
                                                    <div class="form-group row">
                                                        <label class="col-md-3 col-form-label" for="text-input">{{$question->question}} @if($question->hint)<i class="cil-info" data-toggle="tooltip" data-placement="top" title="{{$question->hint}}"></i>@endif </label>
                                                        <div class="col-md-9">
                                                             <i class="c-icon c-icon-2xl cil-calculator" data-toggle="modal" data-target="#productSizeChartModal"></i>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                    @endforeach

                                                @endforeach
                                
                                            @endif
                                            
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit">Save changes</button>
                    </div>
                    </form>
                </div>
            <!-- /.modal-content-->
            </div>
        <!-- /.modal-dialog-->
        </div>
        <!-- End Additional Description Info Modal -->

        <!-- Product Image Modal -->
        <div class="modal fade" id="productImageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Product Images</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <form class="form-horizontal" action="{{ route('myListingPhotoStore') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        @if(isset($listingPhotos))
                        <?php //echo '<pre>'; print_r($listingPhotos); echo $listingPhotos->image_1;?>
                        @endif
                        <input type="hidden" name="listing_id" value="<?=$listing_id;?>" />
                        <div class="modal-body">
                            <div class="fade-in">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="col-12 col-sm-12 col-md-12 mt-1 mb-3" style="text-align: center;">
                                                    <img id="imagesPreview" src="<?= (isset($listingPhotos)) ? url('/').'/uploads/listings/'.$listingPhotos->listing_id.'/'.$listingPhotos->image_1 : '' ?>" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" />
                                                </div>    
                                                <input type="file" class="form-control form-control-solid form-control-lg triggerChange"  name="image_1" accept="image/*"  value="" style="display: none;" />
                                                <input type="file" class="form-control form-control-solid form-control-lg triggerChange"  name="image_2" accept="image/*"  value="" style="display: none;" />
                                                <input type="file" class="form-control form-control-solid form-control-lg triggerChange"  name="image_3" accept="image/*"  value="" style="display: none;" />
                                                <input type="file" class="form-control form-control-solid form-control-lg triggerChange"  name="image_4" accept="image/*"  value="" style="display: none;" />
                                                <input type="file" class="form-control form-control-solid form-control-lg triggerChange"  name="image_5" accept="image/*"  value="" style="display: none;" />
                                                <div class="btn btn-primary uploadImg" id="image_1" style="margin-left: calc(7vw);"> Upload Image </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="card">
                                                    <div class="card-header"><h4>Front View</h4></div>
                                                    <div class="card-body">
                                                        <div class="form-group row">
                                                            <h6>Follow Image GuideLines to reduce the Quality Check failures.</h6>
                                                            <h6>Image Resolution</h6>
                                                            <div>Use clear color images with minimum resolution of 500x500 px.</div>
                                                            <h6>Image GuideLines</h6>
                                                            <div>Upload authentic product photos taken in bright lighting</div>
                                                            <br/>
                                                            <div style="clear: both;"></div>
                                                            <h6>Tips</h6>
                                                            <div>Drag image to the desired position to re-order the images.</div>
                                                            <div>Check out the sample images to ensure you provide the correct Image View. e.g. First Image has to be the Front View</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="col-md-12 mt-3">
                                        <div class="row triggerImg">
                                            <div class="col-md-2">
                                                <div class="boxShadow" style="text-align: center; cursor: pointer; border: 1px solid #eee;">
                                                    <img id="preview_1" class="image_1" src="<?= (isset($listingPhotos)) ? url('/').'/uploads/listings/'.$listingPhotos->listing_id.'/'.$listingPhotos->image_1 : '' ?>" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" />
                                                    <!-- <i class="c-icon c-icon-2xl mt-4 mb-4 cil-image-plus "></i> -->
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="" style="text-align: center; cursor: pointer; border: 1px solid #eee;">
                                                    <img id="preview_2" class="image_2" src="<?= (isset($listingPhotos)) ? url('/').'/uploads/listings/'.$listingPhotos->listing_id.'/'.$listingPhotos->image_2 : '' ?>" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" />
                                                    <!-- <i class="c-icon c-icon-2xl mt-4 mb-4 cil-image-plus triggerImg"></i> -->
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="" style="text-align: center; cursor: pointer; border: 1px solid #eee;">
                                                    <img id="preview_3" class="image_3" src="<?= (isset($listingPhotos)) ? url('/').'/uploads/listings/'.$listingPhotos->listing_id.'/'.$listingPhotos->image_3 : '' ?>" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" />
                                                    <!-- <i class="c-icon c-icon-2xl mt-4 mb-4 cil-image-plus triggerImg"></i> -->
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="" style="text-align: center; cursor: pointer; border: 1px solid #eee;">
                                                    <img id="preview_4" class="image_4" src="<?= (isset($listingPhotos)) ? url('/').'/uploads/listings/'.$listingPhotos->listing_id.'/'.$listingPhotos->image_4 : '' ?>" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" />
                                                    <!-- <i class="c-icon c-icon-2xl mt-4 mb-4 cil-image-plus triggerImg"></i> -->
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="" style="text-align: center; cursor: pointer; border: 1px solid #eee;">
                                                    <img id="preview_5" class="image_5" src="<?= (isset($listingPhotos)) ? url('/').'/uploads/listings/'.$listingPhotos->listing_id.'/'.$listingPhotos->image_5 : '' ?>" onerror="this.onload = null; this.src='{{ url('/') }}/assets/img/no-image.jpg';" />
                                                    <!-- <i class="c-icon c-icon-2xl mt-4 mb-4 cil-image-plus triggerImg"></i> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.col-->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            <button class="btn btn-primary" type="submit">Save changes</button>
                        </div>
                    </form>
                </div>
            <!-- /.modal-content-->
            </div>
        <!-- /.modal-dialog-->
        </div>
        <!-- End Product Image Modal -->

        <!-- Product Size Chart Modal -->
        <div class="modal fade" id="productSizeChartModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Product Size Chart</h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    
                    
                    <form id="sizeChartForm">
                        @csrf
                        <input type="hidden" name="listing_id" value="<?=$listing_id;?>" />
                        <div class="modal-body">
                            <div class="fade-in">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <table class="table table-responsive-sm sizeChart">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <label>Unit</label>
                                                            <!-- <select name="sizeUnit" class="form-control">
                                                                <option value="Kids" {{ (isset($sizeData) && count($sizeData) > 0) ? ($sizeData[0]->sizeFor == "Kids" ? 'selected' : '') : 'selected'}}>Kids</option>
                                                                <option value="Regular" {{ (isset($sizeData) && count($sizeData) > 0) ? ($sizeData[0]->sizeFor == "Regular" ? 'selected' : '') : ''}}>Regular</option>
                                                            </select> -->
                                                        </th>
                                                        <th>Qty.</th>
                                                        <th>Brand Size</th>
                                                        <th>Price</th>    
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                //print_r($sizeData->toArray());die();
                                                function checkSize($sizeData,$value,$listing_id) {
                                                    $d = array_filter($sizeData->toArray(), function($ar) use ($value,$listing_id){
                                                        if(($ar->listing_id == $listing_id) && ($ar->size == $value) ) {
                                                            return $ar;
                                                        } else {
                                                            return false;
                                                        } 
                                                    });
                                                    $d = $d ? array_filter(array_merge(array(), $d)) : '';
                                                    return $d;    
                                                }
                                                @endphp

                                                @if(isset($attributes) && !empty($attributes))
                                                    @foreach($attributes as $akey => $avalue)
                                                        <tr>
                                                            @php
                                                            $sizes = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, $avalue->title, $listing_id) : '';
                                                            @endphp
                                                            <td><input type="checkbox" name="unit[]" value="{{ $avalue->title }}" {{ $sizes ? 'checked' : ''  }} /> {{ $avalue->title }} </td>
                                                            <td><input type="number" name="quantity[]" value="{{ $sizes ? $sizes[0]->quantity : ''  }}"></td>
                                                            <td><input type="number" name="price[]" value="{{ $sizes ? $sizes[0]->price : ''  }}" step="0.01"></td>
                                                            <td><input type="text" name="brand_size[]" value="{{ $sizes ? $sizes[0]->brand_size : ''  }}"></td>
                                                        </tr>    
                                                    @endforeach
                                                @endif
                                                    <!--
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size03 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '0-3', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="0-3" {{ $size03 ? 'checked' : ''  }} /> 0-3 Months</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size03 ? $size03[0]->brand_size : ''  }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size03 ? $size03[0]->price : ''  }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size06 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '0-6', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="0-6" {{ $size06 ? 'checked' : ''  }} /> 0-6 Months</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size06 ? $size06[0]->brand_size : ''  }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size06 ? $size06[0]->price : ''  }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size12 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '1-2', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="1-2" {{ $size12 ? 'checked' : ''  }} /> 1-2 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size12 ? $size12[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size12 ? $size12[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size1011 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '10-11', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="10-11" {{ $size1011 ? 'checked' : ''  }} /> 10-11 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size1011 ? $size1011[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size1011 ? $size1011[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size1112 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '11-12', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="11-12" {{ $size1112 ? 'checked' : ''  }} /> 11-12 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size1112 ? $size1112[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size1112 ? $size1112[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size1213 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '12-13', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="12-13" {{ $size1213 ? 'checked' : ''  }} /> 12-13 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size1213 ? $size1213[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size1213 ? $size1213[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size1218 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '12-18', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="12-18" {{ $size1218 ? 'checked' : ''  }} /> 12-18 Months</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size1218 ? $size1218[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size1218 ? $size1218[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size1314 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '13-14', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="13-14" {{ $size1314 ? 'checked' : ''  }} /> 13-14 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size1314 ? $size1314[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size1314 ? $size1314[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size1415 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '14-15', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="14-15" {{ $size1415 ? 'checked' : ''  }} /> 14-15 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size1415 ? $size1415[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size1415 ? $size1415[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size1516 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '15-16', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="15-16" {{ $size1516 ? 'checked' : ''  }} /> 15-16 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size1516 ? $size1516[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size1516 ? $size1516[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size1824 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '18-24', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="18-24" {{ $size1824 ? 'checked' : ''  }} /> 18-24 Months</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size1824 ? $size1824[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size1824 ? $size1824[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size23 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '2-3', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="2-3" {{ $size23 ? 'checked' : ''  }} /> 2-3 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size23 ? $size23[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size23 ? $size23[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size34 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '3-4', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="3-4" {{ $size34 ? 'checked' : ''  }} /> 3-4 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size34 ? $size34[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size34 ? $size34[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size36 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '3-6', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="3-6" {{ $size36 ? 'checked' : ''  }} /> 3-6 Months</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size36 ? $size36[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size36 ? $size36[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size45 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '4-5', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="4-5" {{ $size45 ? 'checked' : ''  }} /> 4-5 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size45 ? $size45[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size45 ? $size45[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size56 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '5-6', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="5-6" {{ $size56 ? 'checked' : ''  }} /> 5-6 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size56 ? $size56[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{$size56 ? $size56[0]->price : ''}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size612 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '6-12', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="6-12" {{ $size612 ? 'checked' : ''  }} /> 6-12 Months</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size612 ? $size612[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size612 ? $size612[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size67 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '6-7', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="6-7" {{ $size67 ? 'checked' : ''  }} /> 6-7 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size67 ? $size67[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size67 ? $size67[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size69 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '6-9', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="6-9" {{ $size69 ? 'checked' : ''  }} /> 6-9 Months</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size69 ? $size69[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size69 ? $size69[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size78 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '7-8', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="7-8" {{ $size78 ? 'checked' : ''  }} /> 7-8 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size78 ? $size78[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size78 ? $size78[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size89 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '8-9', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="8-9" {{ $size89 ? 'checked' : ''  }} /> 8-9 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size89 ? $size89[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size89 ? $size89[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size910 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '9-10', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="9-10" {{ $size910 ? 'checked' : ''  }} /> 9-10 Years</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size910 ? $size910[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size910 ? $size910[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="kidsSize">
                                                        @php
                                                        $size912 = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '9-12', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="9-12" {{ $size912 ? 'checked' : ''  }} /> 9-12 Months</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size912 ? $size912[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size912 ? $size912[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $size3xs = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '3xs', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="3xs" {{ $size3xs ? 'checked' : ''  }} /> 3XS</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size3xs ? $size3xs[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size3xs ? $size3xs[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $sizexxs = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, 'xxs', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="xxs" {{ $sizexxs ? 'checked' : ''  }} /> XXS</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $sizexxs ? $sizexxs[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $sizexxs ? $sizexxs[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $sizexs = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, 'xs', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="xs" {{ $sizexs ? 'checked' : ''  }} /> XS</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $sizexs ? $sizexs[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $sizexs ? $sizexs[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $sizes = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, 's', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="s" {{ $sizes ? 'checked' : ' '  }} /> S</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $sizes ? $sizes[0]->brand_size : ''}}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $sizes ? $sizes[0]->price : ''}}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $sizem = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, 'm', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="m" {{$sizem ? 'checked' : ''}} /> M</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $sizem ? $sizem[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $sizem ? $sizem[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $sizel = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, 'l', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="l" {{$sizel?'checked':''}} /> L</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $sizel ? $sizel[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $sizel ? $sizel[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $sizexl = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, 'xl', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="xl" {{$sizexl ? 'checked' : '' }} /> XL</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $sizexl ? $sizexl[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $sizexl ? $sizexl[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $sizexxl = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, 'xxl', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="xxl" {{ $sizexxl ? 'checked' : ''}} /> XXL</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $sizexxl ? $sizexxl[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $sizexxl ? $sizexxl[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $size3xl = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '3xl', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="3xl" {{ $size3xl ? 'checked' : ''}} /> 3XL</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size3xl ? $size3xl[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size3xl ? $size3xl[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $size4xl = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '4xl', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="4xl" {{ $size4xl ? 'checked' : ''}} /> 4XL</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size4xl ? $size4xl[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size4xl ? $size4xl[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $size5xl = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '5xl', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="5xl" {{ $size5xl ? 'checked' : '' }} /> 5XL</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size5xl ? $size5xl[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size5xl ? $size5xl[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $size6xl = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '6xl', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="6xl" {{ $size6xl ? 'checked' : ''}} /> 6XL</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size6xl ? $size6xl[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size6xl ? $size6xl[0]->price : '' }}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $size7xl = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '7xl', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="7xl" {{ $size7xl ? 'checked' : ''}} /> 7XL</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size7xl ? $size7xl[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size7xl ? $size7xl[0]->price  : ''}}"></td>
                                                    </tr>
                                                    <tr class="regularSize">
                                                        @php
                                                        $size8xl = (isset($sizeData) && count($sizeData) > 0) ? checkSize($sizeData, '8xl', $listing_id) : '';
                                                        @endphp
                                                        <td><input type="checkbox" name="unit[]" value="8xl" {{ $size8xl ? 'checked' : '' }} /> 8XL</td>
                                                        <td><input type="text" name="brand_size[]" value="{{ $size8xl ? $size8xl[0]->brand_size : '' }}"></td>
                                                        <td><input type="number" name="price[]" value="{{ $size8xl ? $size8xl[0]->price : '' }}"></td>
                                                    </tr>
                                                    -->
                                                </tbody>
                                            </table>
                                            
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="col-md-12 mt-3">
                                        
                                    </div>
                                    <!-- /.col-->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            <button class="btn btn-success sizeSent">Size Update</button>
                        </div>
                    </form>
                </div>
            <!-- /.modal-content-->
            </div>
        <!-- /.modal-dialog-->
        </div>
        <!-- End Product Size Chart Modal -->

@endsection


@section('javascript')
<script type="text/javascript">
    $(document).on('click', '.triggerImg img', function(){
        //alert($(this).attr('class'));
        $('.uploadImg').attr('id', $(this).attr('class'));
        $('div').removeClass('boxShadow');
        $(this).parent('div').addClass('boxShadow');

        if($(this).attr('src')) {
            $('#imagesPreview').attr('src', $(this).attr('src'));
        }
    });

    $(document).on('click', '.uploadImg', function(){
        var id = $(this).attr('id');
        $('input[name="'+id+'"]').trigger('click');
    });

    $('.triggerChange').on('change', function() {
        imagesPreview(this, '#imagesPreview');
    });
    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    //console.log(input.name);
                    var className = input.name;
                    //$($.parseHTML('<img width="70">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                    $(placeToInsertImagePreview).attr('src', event.target.result);
                    $('.'+className).attr('src', event.target.result);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };
    $(document).on('click', '.requestApproval', function(){
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
            data: {status:2},
            success:function(result){
                $('.positionAbsolute').find('.qc').removeClass('requestApproval');
                $('.positionAbsolute').find('.qc').text('Request under Approval');   
            }
        });
    }

    $('.regularSize').hide();
    $('.sizeChart').find('[type="text"]').prop('disabled', true);
    $('.sizeChart').find('[type="number"]').prop('disabled', true);
    $(document).on('change', '[name="sizeUnit"]', function(){
        var sizeType = $(this).val();
        if(sizeType == 'Kids') {
            $('.kidsSize').show();
            $('.regularSize').hide();
            $('.regularSize').find('[type="text"]').prop('disabled', true);
            $('.regularSize').find('[type="number"]').prop('disabled', true);
            $('.regularSize').find('[type="text"]').val('');
            $('.regularSize').find('[type="number"]').val('');
            $('.regularSize').find('[type="checkbox"]').prop('checked', false);
        }

        if(sizeType == 'Regular') {
            $('.kidsSize').hide();
            $('.regularSize').show();   
            // $('.regularSize').find('[type="text"]').prop('disabled', false);
            $('.kidsSize').find('[type="text"]').prop('disabled', true);
            $('.kidsSize').find('[type="number"]').prop('disabled', true);
            $('.kidsSize').find('[type="text"]').val('');
            $('.kidsSize').find('[type="number"]').val('');
            $('.kidsSize').find('[type="checkbox"]').prop('checked', false);
        }
    });
    $(document).on('click', '[name="unit[]"]', function(){
        $(this).parent().parent().find('[type="text"]').prop('disabled', true);
        $(this).parent().parent().find('[type="number"]').prop('disabled', true);
        if($(this).prop("checked") == true){
            $(this).parent().parent().find('[type="text"]').prop('disabled', false);
            $(this).parent().parent().find('[type="number"]').prop('disabled', false);
        }
    });

    $(document).on('click', '.sizeSent', function(){
        $.ajax({
            url:"{{route('storeListingSizeChartData')}}",
            type: "POST",
            data: $('#sizeChartForm').serialize(),
            success: function(result){
                $('#productSizeChartModal').find('.close').trigger('click');
            }
        });
        return false;
    });


    @if(isset($sizeData))
        @if(isset($sizeData[0]->sizeFor) && $sizeData[0]->sizeFor == "Regular")
            $('.regularSize').show();
            $('.kidsSize').hide();
        @endif

        @foreach($sizeData as $skey => $svalue)
            $('input[name="unit[]"][value="{{ $svalue->size }}"]').parent().parent().find('[type="text"]').prop('disabled', false);
            $('input[name="unit[]"][value="{{ $svalue->size }}"]').parent().parent().find('[type="number"]').prop('disabled', false);
        @endforeach
    @endif
</script>
@endsection