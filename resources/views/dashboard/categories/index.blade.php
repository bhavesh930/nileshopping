@extends('dashboard.base')

<style type="text/css">

  td:nth-child(5) {

      width: 165px;

  }

  tr td:nth-child(5) a.btn-primary {

      width: 50px;

      display: inline-block;

  }

  tr td:nth-child(5) a.btn-info {

      width: 84px;

      display: inline-block;

      margin-top: 0;

  }

</style>

@section('content')



        <div class="container-fluid">

          <div class="animated fadeIn">

            <div class="row">

              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">

                <div class="card">

                    <div class="card-header">

                      <i class="fa fa-align-justify"></i>{{ __('Category') }}</div>

                    <div class="card-body">

                        <div class="row"> 

                          <a href="{{ route('category.create') }}" class="btn btn-primary m-2">{{ __('Add Category') }}</a>

                        </div>

                        <br>

                        <table class="table table-responsive-sm table-striped">

                        <thead>

                          <tr>

                            <th>Name</th>

                            <th>Description</th>

                            <th>Image</th>

                            <th>Parent Category</th>

                            <!-- <th></th> -->

                            <th></th>

                            <!-- <th></th> -->

                          </tr>

                        </thead>

                        <tbody>

                          @foreach($categories as $category)

                            <?php 

                            $categoryQuestionData = App\Models\Question::where('category_id',$category->id)->get();

                            

                            $paretCategoryName = '';

                            if($category->parent_id){

                              $categoryData = App\Models\Category::find($category->parent_id);

                              $paretCategoryName = $categoryData->name;  

                            }



                            $isVerticalCategory = App\Models\Category::isVerticalCategory($category->id);

                            ?>

                            <tr>

                              <td><strong>{{ $category->name }}</strong></td>

                              <td>{{ $category->description }}</td>

                              <td>{{ $category->image }}</td>

                              <td>{{ $paretCategoryName }}</td>

                              <!-- <td>

                                <a href="{{ url('/category/' . $category->id) }}" class="btn btn-block btn-primary">View</a>

                              </td> -->

                              <td>

                                <a href="{{ url('/category/' . $category->id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>

                                @if($isVerticalCategory)

                                <a href="{{ url('/category/questionList/' . $category->id) }}" class="btn btn-block btn-info">Question</a>
                                <a href="{{ url('/category/attributes/' . $category->id) }}" class="btn btn-block btn-success">Attributes</a>

                                @endif

                              </td>

                              <!-- <td>

                                <form action="{{ route('category.destroy', $category->id ) }}" method="POST">

                                    @method('DELETE')

                                    @csrf

                                    <button class="btn btn-block btn-danger">Delete</button>

                                </form>

                              </td> -->

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



