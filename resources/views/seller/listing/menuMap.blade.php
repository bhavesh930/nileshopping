
<style type="text/css">
	.select2-dropdown{
		z-index: 1050 !important;
	}
	.select2-container{
		text-align: left;
		width: 100% !important;
	}
	.select2-search__field{
		width: 100% !important;
	}
</style>
<form action="{{ route('listingMenuMappingStore') }}" method="post">
	@csrf
	<input type="hidden" name="listing_id" value="{{ $listing->id }}">
	<div class="row col-md-12">
		<div class="col-md-12 moduleAssignView">
			<div class="form-group m-form__group row">
				<label class="form-label">Select Menu</label>
				<select class="form-control m-select2" placeholder="Select Menu" id="menu_id" name="menu_id">
					<option value="">Select One</option>
                    @if($menu_list)
                    @foreach($menu_list as $menu)
                    <option value="{{$menu->id}}" <?= (isset($listing) &&  $listing->menu_id == $menu->id) ? 'selected' : ''?>>{{$menu->name}}</option>
                    @endforeach
                    @endif
				</select>
			</div>
		</div>
		<div class="col-md-12 moduleAssignView">
			<div class="form-group m-form__group row">
				<label class="form-label">Assign Menu</label>
				<select class="form-control m-select2" placeholder="Modules" id="hastags" name="hastags[]" multiple>
					@if($listing && $listing->hastags)
						<?php 
						$hastags = explode(',', $listing->hastags);
						?>
	                    @foreach($hastags as $hastag)
	                    <option value="{{$hastag}}" selected="">{{$hastag}}</option>
	                    @endforeach
                    @endif
				</select>
			</div>
		</div>

		<div class="m-portlet__foot m-portlet__foot--fit">
			<div class="m-form__actions">
				<div class="row">
					<div class="col-10">
						<button type="Submit" class="btn btn-success">
							Submit
						</button>
					</div>
				</div>
			</div>
		</div>

	</div>
</form>

<script type="text/javascript">
	$('#menu_id').select2({
    	placeholder:"Select Menu"
    });
    $('#hastags').select2({
    	tags: true,
    	placeholder:"Hastags"
    });
</script>