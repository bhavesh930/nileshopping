@extends('dashboard.base')

<style type="text/css">
  .category-toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 18px;
  }

  .category-breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
      margin-bottom: 16px;
  }

  .category-breadcrumb a,
  .category-breadcrumb span {
      font-size: 14px;
  }

  .category-breadcrumb .separator {
      color: #8a93a2;
  }

  .category-name {
      display: flex;
      flex-direction: column;
      gap: 4px;
  }

  .category-name strong {
      font-size: 15px;
  }

  .category-actions {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
      justify-content: flex-end;
      min-width: 250px;
  }

  .category-actions .btn {
      margin: 0;
  }

  .category-empty {
      padding: 36px 16px;
      text-align: center;
      color: #636f83;
  }
</style>

@section('content')

        <div class="container-fluid">

          <div class="animated fadeIn">

            <div class="row">

              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">

                <div class="card">

                    <div class="card-header">

                      <i class="fa fa-align-justify"></i>
                      {{ $parentCategory ? __('Child Categories') : __('Categories') }}
                    </div>

                    <div class="card-body">

                        @if (Session::has('success'))
                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                              {{ Session::get('success') }}
                              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                        @endif

                        <div class="category-breadcrumb">
                          <a href="{{ route('category.index') }}">{{ __('Categories') }}</a>
                          @foreach($breadcrumbs as $breadcrumb)
                            <span class="separator">/</span>
                            @if(!$loop->last)
                              <a href="{{ route('category.index', ['parent_id' => $breadcrumb->id]) }}">{{ $breadcrumb->name }}</a>
                            @else
                              <span>{{ $breadcrumb->name }}</span>
                            @endif
                          @endforeach
                        </div>

                        <div class="category-toolbar">
                          <div>
                            @if($parentCategory)
                              <h4 class="mb-1">{{ $parentCategory->name }}</h4>
                              <p class="text-muted mb-0">{{ __('Manage direct child categories under this parent.') }}</p>
                            @else
                              <h4 class="mb-1">{{ __('Parent Categories') }}</h4>
                              <p class="text-muted mb-0">{{ __('Start from a parent category, then drill down to its children.') }}</p>
                            @endif
                          </div>

                          <div>
                            @if($parentCategory && $parentCategory->parent_id)
                              <a href="{{ route('category.index', ['parent_id' => $parentCategory->parent_id]) }}" class="btn btn-secondary">
                                {{ __('Back') }}
                              </a>
                            @elseif($parentCategory)
                              <a href="{{ route('category.index') }}" class="btn btn-secondary">
                                {{ __('Back') }}
                              </a>
                            @endif
                            <a href="{{ route('category.create', $parentCategory ? ['parent_id' => $parentCategory->id] : []) }}" class="btn btn-primary">
                              {{ $parentCategory ? __('Add Child Category') : __('Add Parent Category') }}
                            </a>
                          </div>
                        </div>

                        <table class="table table-responsive-sm table-striped">

                        <thead>

                          <tr>

                            <th>{{ __('Category') }}</th>

                            <th>{{ __('Description') }}</th>

                            <th>{{ __('Image') }}</th>

                            <th>{{ __('Children') }}</th>

                            <th>{{ __('Setup') }}</th>

                            <th class="text-right">{{ __('Actions') }}</th>

                          </tr>

                        </thead>

                        <tbody>

                          @forelse($categories as $category)

                            <?php
                              $isVerticalCategory = App\Models\Category::isVerticalCategory($category->id);
                              $questionCount = $questionCounts[$category->id] ?? 0;
                              $attributeCount = $attributeCounts[$category->id] ?? 0;
                            ?>

                            <tr>

                              <td>
                                <div class="category-name">
                                  <strong>{{ $category->name }}</strong>
                                  @if($category->children_count > 0)
                                    <span class="badge badge-info">{{ __('Parent') }}</span>
                                  @elseif($isVerticalCategory)
                                    <span class="badge badge-success">{{ __('Final Category') }}</span>
                                  @else
                                    <span class="badge badge-secondary">{{ __('Category') }}</span>
                                  @endif
                                </div>
                              </td>

                              <td>{{ $category->description }}</td>

                              <td>{{ $category->image }}</td>

                              <td>
                                <span class="badge badge-light">{{ $category->children_count }}</span>
                              </td>

                              <td>
                                @if($isVerticalCategory)
                                  <span class="badge badge-info">{{ __('Questions') }}: {{ $questionCount }}</span>
                                  <span class="badge badge-success">{{ __('Attributes') }}: {{ $attributeCount }}</span>
                                @else
                                  <span class="text-muted">{{ __('Child categories can be managed from here.') }}</span>
                                @endif
                              </td>

                              <td>
                                <div class="category-actions">
                                  @if($category->children_count > 0)
                                    <a href="{{ route('category.index', ['parent_id' => $category->id]) }}" class="btn btn-sm btn-outline-info">
                                      {{ __('View Children') }}
                                    </a>
                                  @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                      {{ __('No Children') }}
                                    </button>
                                  @endif

                                  <a href="{{ route('category.create', ['parent_id' => $category->id]) }}" class="btn btn-sm btn-outline-success">
                                    {{ __('Add Child') }}
                                  </a>

                                  <a href="{{ url('/category/' . $category->id . '/edit') }}" class="btn btn-sm btn-primary">
                                    {{ __('Edit') }}
                                  </a>

                                  @if($isVerticalCategory)
                                    <a href="{{ url('/category/questionList/' . $category->id) }}" class="btn btn-sm btn-info">
                                      {{ __('Question') }}
                                    </a>
                                    <a href="{{ url('/category/attributes/' . $category->id) }}" class="btn btn-sm btn-success">
                                      {{ __('Attributes') }}
                                    </a>
                                  @endif
                                </div>
                              </td>

                            </tr>

                          @empty
                            <tr>
                              <td colspan="6">
                                <div class="category-empty">
                                  <p class="mb-3">{{ __('No categories found at this level.') }}</p>
                                  <a href="{{ route('category.create', $parentCategory ? ['parent_id' => $parentCategory->id] : []) }}" class="btn btn-primary">
                                    {{ $parentCategory ? __('Add Child Category') : __('Add Parent Category') }}
                                  </a>
                                </div>
                              </td>
                            </tr>
                          @endforelse

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
