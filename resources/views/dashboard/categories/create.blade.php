@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                 @if (Session::has('errors'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h4 class="alert-heading">Error!</h4>
                        <p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </p>

                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Create Category') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ isset($editData) ? route('category.update', $editData->id) : route('category.store') }}">
                            @csrf

                            @if(isset($editData))
                            @method('PUT')
                            <!-- <input type="hidden" name="id" value="{{ $editData->id }}" id="categoryId"/> -->
                            @endif
                            <div class="form-group row">
                                <label>Category</label>
                                <input class="form-control" type="text" placeholder="{{ __('Title') }}" name="name" value="<?= isset($editData) ? $editData->name : ''?>" required autofocus>
                            </div>

                            <div class="form-group row">
                                <label>Description</label>
                                <textarea class="form-control" id="textarea-input" name="description" rows="9" placeholder="{{ __('Content..') }}" required><?= isset($editData) ? $editData->description : ''?></textarea>
                            </div>

                            <div class="form-group row">
                                <label>Parent Category</label>
                                <select class="form-control" name="parent_id">
                                    <option value="">Select Parent Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" <?= (isset($editData) && ($editData->parent_id == $category->id)) || (!isset($editData) && isset($selectedParentId) && ($selectedParentId == $category->id)) ? 'selected' : ''?> >{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="btn btn-block btn-success" type="submit">{{ __('Add') }}</button>
                            <a href="{{ route('category.index') }}" class="btn btn-block btn-primary">{{ __('Return') }}</a> 
                        </form>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection

@section('javascript')

@endsection
