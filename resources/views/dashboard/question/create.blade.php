@extends('dashboard.base')
<style type="text/css">
    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 20px;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        opacity: 1 !important;
    }
    .modal-content{
        margin-top: 80px;
    }
</style>
@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-10 col-lg-12 col-xl-12">
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

                <?php  /*if(isset($editData)):
                    echo '<pre>'; print_r(json_encode($editData));die();
                endif;*/ ?>
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i> {{ __('Create Question') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ isset($editData) ? route('question.update', $id) : route('question.store') }}">
                            @csrf

                            @if(isset($editData))
                            @method('PUT')
                            
                            @endif
                            <div class="form-group row">
                                <div class="col-sm-5">
                                    <label>Category</label>
                                    <select class="form-control" name="parent_id" @if(isset($id)) disabled="" @endif>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" <?= isset($editData) && ($id == $category->id) ? 'selected' : ''?> >{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-2 mt-4">
                                    <span class="btn btn-block btn-primary addSection">Add Section</span>    
                                </div>
                            </div>
                            <div id="sectionContent">
                                @if(isset($id) && count($editData) > 0)
                                    @foreach($editData as $key => $value)
                                    <input type="hidden" name="sectionName[<?=$key+1?>]" value="{{ ($value['section']) ? $value['section'] : '' }}" />
                                    <div class="sectionAdd" id="section_{{ $key+1 }}">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Section</label>
                                                <input class="form-control sectionInput" type="text" placeholder="{{ __('Title') }}" name="sectionName[<?=$key+1?>]" value="{{ ($value['section']) ? $value['section'] : '' }}" required autofocus disabled="">
                                            </div>
                                            <div class="col-2 mt-4">
                                                <span class="btn btn-block btn-primary addQuestion">Add</span>    
                                            </div>
                                            <!-- <div class="col-3 mt-4">
                                                <span class="btn btn-block btn-danger removeSection">Remove Section</span>
                                            </div> -->
                                        </div>
                                        <div class="questionAdd">
                                            @foreach(json_decode(json_encode($value['question'])) as $qkey => $qvalue)
                                            <div class="form-group row innerElements" id="sectionQuestion_{{$key+1}}_{{$qvalue->id}}">
                                                <div class="col-sm-1"></div>
                                                <div class="col-sm-5">
                                                    <label>Question</label>
                                                    <input class="form-control questionInput" type="text" placeholder="{{ __('Title') }}" name="questionName[<?= $key+1?>][<?= $qvalue->id?>]" value="{{ isset($qvalue) ? $qvalue->question : '' }}" required autofocus>
                                                </div>
                                                <div class="col-2 mt-4">
                                                    <span class="btn btn-block btn-info addQuestionInfo">Info</span>    
                                                </div>
                                                <div class="col-2 mt-4">
                                                    <span class="btn btn-block btn-danger removeQuestion" data-rel="{{$qvalue->id}}">Delete</span>
                                                </div>
                                                <!-- ADD MODAL -->
                                                <div class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Question Information</h4>
                                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group row">
                                                                    <div class="col-sm-6">
                                                                        <label>Question Hint</label>
                                                                        <input class="form-control questionHintInput" type="text" placeholder="{{ __('Question Hint') }}" name="questionHint[<?= $key+1?>][<?= $qvalue->id?>]" value="{{ $qvalue->hint}}" autofocus>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <label>Question Placeholder</label>
                                                                        <input class="form-control questionPlaceholderInput" type="text" placeholder="{{ __('Question Placeholder') }}" name="questionPlaceholder[<?= $key+1?>][<?= $qvalue->id?>]" value="{{ $qvalue->placeholder}}" autofocus>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-sm-6">
                                                                        <div>
                                                                            <label>Is Required Field</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mr-1">
                                                                            <input class="form-check-input" type="Checkbox" name="questionRequired[<?= $key+1?>][<?= $qvalue->id?>]" {{ $qvalue->required == 1 ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label" for="inline-radio1">Required</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-sm-9">
                                                                        <div>
                                                                            <label>Answer Type</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mr-1">
                                                                            <input class="form-check-input" id="inline-radio1" type="radio" value="Text" name="answerType[<?= $key+1?>][<?= $qvalue->id?>]" {{ $qvalue->type == 'Text' ? 'checked="checked"' : '' }} >
                                                                            <label class="form-check-label" for="inline-radio1">Text</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mr-1">
                                                                            <input class="form-check-input" id="inline-radio2" type="radio" value="Checkbox" name="answerType[<?= $key+1?>][<?= $qvalue->id?>]" {{ $qvalue->type == 'Checkbox' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label" for="inline-radio2">Checkbox</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mr-1">
                                                                            <input class="form-check-input" id="inline-radio3" type="radio" value="Radio" name="answerType[<?= $key+1?>][<?= $qvalue->id?>]" {{ $qvalue->type == 'Radio' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label" for="inline-radio3">Radio</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mr-1">
                                                                            <input class="form-check-input" id="inline-radio4" type="radio" value="Dropdown" name="answerType[<?= $key+1?>][<?= $qvalue->id?>]" {{ $qvalue->type == 'Dropdown' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label" for="inline-radio4">Dropdown</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mr-1">
                                                                            <input class="form-check-input" id="inline-radio5" type="radio" value="Textarea" name="answerType[<?= $key+1?>][<?= $qvalue->id?>]" {{ $qvalue->type == 'Textarea' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label" for="inline-radio5">Textarea</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline mr-1">
                                                                            <input class="form-check-input" id="inline-radio6" type="radio" value="Size" name="answerType[<?= $key+1?>][<?= $qvalue->id?>]" {{ $qvalue->type == 'Size' ? 'checked="checked"' : '' }}>
                                                                            <label class="form-check-label" for="inline-radio5">Size</label>
                                                                        </div>              
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="form-group row answerOption" style="overflow-x: auto; max-height: calc(40vh); display: {{ ($qvalue->type == 'Checkbox' || $qvalue->type == 'Radio' || $qvalue->type == 'Dropdown') ? 'block' : 'none' }}">
                                                                    <div class="col-sm-12">
                                                                        <div>
                                                                            <label>Answer Option</label>
                                                                        </div>
                                                                        <div class="addOptions">
                                                                        @if($qvalue->type == 'Checkbox' || $qvalue->type == 'Radio' || $qvalue->type == 'Dropdown')
                                                                            @php
                                                                            $questionOption = App\Models\QuestionOption::where('question_id', $qvalue->id)->get();
                                                                            @endphp
                                                                            @if($questionOption)
                                                                            @foreach($questionOption as $option)
                                                                            <div class="form-group row optionElement" id="optionElement_{{$key+1}}_{{$qvalue->id}}_{{$option->id}}">
                                                                                <div class="col-sm-6">
                                                                                    <input class="form-control optionInput" type="text" placeholder="{{ __('Enter Option') }}" name="option[<?=$key+1?>][<?=$qvalue->id?>][<?=$option->id?>]" value="{{$option->option}}" required autofocus>
                                                                                </div>
                                                                                <div class="col-2">
                                                                                    <span class="btn btn-block btn-primary addOption">Add</span>    
                                                                                </div>
                                                                                <div class="col-3">
                                                                                    <span class="btn btn-block btn-danger removeOption" data-rel="{{$option->id}}">Remove</span>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                            @endif       
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                                                                <!-- <button class="btn btn-primary" type="button">Save changes</button> -->
                                                            </div>
                                                        </div>
                                                    <!-- /.modal-content-->
                                                    </div>
                                                    <!-- /.modal-dialog-->
                                                </div>
                                                <!-- END MODAL -->
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                <div class="sectionAdd" id="section_1">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Section</label>
                                            <input class="form-control sectionInput" type="text" placeholder="{{ __('Title') }}" name="sectionName[1]" value="" required autofocus>
                                        </div>
                                        <div class="col-2 mt-4">
                                            <span class="btn btn-block btn-primary addQuestion">Add</span>    
                                        </div>
                                        <div class="col-3 mt-4">
                                            <span class="btn btn-block btn-danger removeSection">Remove Section</span>
                                        </div>
                                    </div>
                                    <div class="questionAdd">
                                        <div class="form-group row innerElements" id="sectionQuestion_1_1">
                                            <div class="col-sm-1"></div>
                                            <div class="col-sm-5">
                                                <label>Question</label>
                                                <input class="form-control questionInput" type="text" placeholder="{{ __('Title') }}" name="questionName[1][1]" value="" required autofocus>
                                            </div>
                                            <div class="col-2 mt-4">
                                                <span class="btn btn-block btn-info addQuestionInfo">Info</span>    
                                            </div>
                                            <div class="col-2 mt-4">
                                                <span class="btn btn-block btn-danger removeQuestion">Delete</span>
                                            </div>
                                            <!-- ADD MODAL -->
                                            <div class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Question Information</h4>
                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group row">
                                                                <div class="col-sm-6">
                                                                    <label>Question Hint</label>
                                                                    <input class="form-control questionHintInput" type="text" placeholder="{{ __('Question Hint') }}" name="questionHint[1][1]" value="" autofocus>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label>Question Placeholder</label>
                                                                    <input class="form-control questionPlaceholderInput" type="text" placeholder="{{ __('Question Placeholder') }}" name="questionPlaceholder[1][1]" value="" autofocus>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-sm-12">
                                                                    <div>
                                                                        <label>Answer Type</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline mr-1">
                                                                        <input class="form-check-input" id="inline-radio1" type="radio" value="Text" name="answerType[1][1]" checked>
                                                                        <label class="form-check-label" for="inline-radio1">Text</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline mr-1">
                                                                        <input class="form-check-input" id="inline-radio2" type="radio" value="Checkbox" name="answerType[1][1]">
                                                                        <label class="form-check-label" for="inline-radio2">Checkbox</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline mr-1">
                                                                        <input class="form-check-input" id="inline-radio3" type="radio" value="Radio" name="answerType[1][1]">
                                                                        <label class="form-check-label" for="inline-radio3">Radio</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline mr-1">
                                                                        <input class="form-check-input" id="inline-radio4" type="radio" value="Dropdown" name="answerType[1][1]">
                                                                        <label class="form-check-label" for="inline-radio4">Dropdown</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline mr-1">
                                                                        <input class="form-check-input" id="inline-radio5" type="radio" value="Textarea" name="answerType[1][1]">
                                                                        <label class="form-check-label" for="inline-radio5">Textarea</label>
                                                                    </div> 
                                                                    <div class="form-check form-check-inline mr-1">
                                                                        <input class="form-check-input" id="inline-radio6" type="radio" value="Size" name="answerType[1][1]">
                                                                        <label class="form-check-label" for="inline-radio6">Size (For cloths)</label>
                                                                    </div>             
                                                                </div>
                                                            </div>
                                                            <div class="form-group row answerOption" style="display: none;">
                                                                <div class="col-sm-12">
                                                                    <div>
                                                                        <label>Answer Option</label>
                                                                    </div>
                                                                    <div class="addOptions">
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                                                            <!-- <button class="btn btn-primary" type="button">Save changes</button> -->
                                                        </div>
                                                    </div>
                                                <!-- /.modal-content-->
                                                </div>
                                                <!-- /.modal-dialog-->
                                            </div>
                                            <!-- END MODAL -->
                                        </div>
                                    </div>
                                </div>
                                @endif
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
        </div>

        <div id="SectionClonePart" style="display: none;">
            <div class="sectionAdd">
                <div class="form-group row">
                    <div class="col-sm-6">
                        <label>Section</label>
                        <input class="form-control sectionInput" type="text" placeholder="{{ __('Title') }}" name="sectionName[]" value="" required autofocus>
                    </div>
                    <div class="col-2 mt-4">
                        <span class="btn btn-block btn-primary addQuestion">Add</span>    
                    </div>
                    <div class="col-3 mt-4">
                        <span class="btn btn-block btn-danger removeSection">Remove Section</span>
                    </div>
                </div>
                <div class="questionAdd">
                    <div class="form-group row innerElements">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-5">
                            <label>Question</label>
                            <input class="form-control questionInput" type="text" placeholder="{{ __('Title') }}" name="questionName[]" value="" required autofocus>
                        </div>
                        <div class="col-2 mt-4">
                            <span class="btn btn-block btn-info addQuestionInfo">Info</span>    
                        </div>
                        <div class="col-2 mt-4">
                            <span class="btn btn-block btn-danger removeQuestion">Delete</span>
                        </div>
                        <!-- ADD MODAL -->
                        <div class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content" style="overflow-y: auto; height: calc(90vh);">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Question Information</h4>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Question Hint</label>
                                                <input class="form-control questionHintInput" type="text" placeholder="{{ __('Question Hint') }}" name="questionHint[]" value="" autofocus>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Question Placeholder</label>
                                                <input class="form-control questionPlaceholderInput" type="text" placeholder="{{ __('Question Placeholder') }}" name="questionPlaceholder[]" value="" autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <div>
                                                    <label>Answer Type</label>
                                                </div>
                                                <div class="form-check form-check-inline mr-1">
                                                    <input class="form-check-input" id="inline-radio1" type="radio" value="Text" name="answerType[]" checked>
                                                    <label class="form-check-label" for="inline-radio1">Text</label>
                                                </div>
                                                <div class="form-check form-check-inline mr-1">
                                                    <input class="form-check-input" id="inline-radio2" type="radio" value="Checkbox" name="answerType[]">
                                                    <label class="form-check-label" for="inline-radio2">Checkbox</label>
                                                </div>
                                                <div class="form-check form-check-inline mr-1">
                                                    <input class="form-check-input" id="inline-radio3" type="radio" value="Radio" name="answerType[]">
                                                    <label class="form-check-label" for="inline-radio3">Radio</label>
                                                </div>
                                                <div class="form-check form-check-inline mr-1">
                                                    <input class="form-check-input" id="inline-radio4" type="radio" value="Dropdown" name="answerType[]">
                                                    <label class="form-check-label" for="inline-radio4">Dropdown</label>
                                                </div>
                                                <div class="form-check form-check-inline mr-1">
                                                    <input class="form-check-input" id="inline-radio5" type="radio" value="Textarea" name="answerType[]">
                                                    <label class="form-check-label" for="inline-radio5">Textarea</label>
                                                </div>
                                                <div class="form-check form-check-inline mr-1">
                                                    <input class="form-check-input" id="inline-radio6" type="radio" value="Size" name="answerType[]">
                                                    <label class="form-check-label" for="inline-radio6">Size (For cloths)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row answerOption" style="display: none;">
                                            <div class="col-sm-12">
                                                <div>
                                                    <label>Answer Option</label>
                                                </div>
                                                <div class="addOptions">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                                        <!-- <button class="btn btn-primary" type="button">Save changes</button> -->
                                    </div>
                                </div>
                            <!-- /.modal-content-->
                            </div>
                            <!-- /.modal-dialog-->
                        </div>
                        <!-- END MODAL -->
                    </div>
                </div>
            </div>
        </div>

        <div id="optionDiv" style="display: none;">
            <div class="form-group row optionElement">
                <div class="col-sm-6">
                    <input class="form-control optionInput" type="text" placeholder="{{ __('Enter Option') }}" name="option[]" value="" required autofocus>
                </div>
                <div class="col-2">
                    <span class="btn btn-block btn-primary addOption">Add</span>    
                </div>
                <div class="col-3">
                    <span class="btn btn-block btn-danger removeOption">Remove</span>
                </div>
            </div>
        </div>

        <div id="sizeOption" style="display: none;">
            <div class="form-group row optionElement">
                <div class="col-sm-2 mb-3">
                    <label>Unit</label>
                    <select name="sizeUnit" class="form-control">
                        <option value="Kids" selected>Kids</option>
                        <option value="Regular">Regular</option>
                    </select>
                </div>
                <div class="col-12 kids">
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="0-3" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">0-3 Months</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="0-6" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">0-6 Months</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="1-2" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">1-2 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="10-11" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">10-11 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="11-12" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">11-12 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="12-13" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">12-13 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="12-18" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">12-18 Months</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="13-14" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">13-14 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="14-15" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">14-15 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="15-16" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">15-16 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="18-24" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">18-24 Months</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="2-3" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">2-3 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="3-4" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">3-4 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="3-6" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">3-6 Months</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="4-5" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">4-5 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="5-6" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">5-6 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="6-12" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">6-12 Months</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="6-7" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">6-7 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="6-9" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">6-9 Months</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="7-8" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">7-8 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="8-9" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">8-9 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="9-10" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">9-10 Years</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="9-12" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">9-12 Months</label>
                    </div>
                </div>
                <div class="col-sm-12 Regular" style="display: none;">
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="3xs" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">3XS</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="xxs" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">XXS</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="xs" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">XS</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="s" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">S</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="m" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">M</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="l" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">L</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="xl" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">XL</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="xxl" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">XXL</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="3xl" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">3XL</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="4xl" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">4XL</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="5xl" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">5XL</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="6xl" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">6XL</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="7xl" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">7XL</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" id="inline-checkbox6" type="checkbox" value="8xl" name="answerType[]" />
                        <label class="form-check-label" for="inline-checkbox6">8XL</label>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).on('click', '.addQuestion', function(){
        var questionID = $(this).parents('.sectionAdd').find('.questionAdd').find('.innerElements:last').attr('id');
        var splitQuestion = questionID.split('_');
        var incrementQuestion = parseInt(splitQuestion[2]) + 1;
        var sectionID = splitQuestion[1];

        var question = $('#SectionClonePart .questionAdd .row:first').clone();
        question.find('input[type=text]').val('');
        
        $(this).parents('.sectionAdd').find('.questionAdd').append(question);
        $(this).parents('.sectionAdd').find('.questionAdd').find('.innerElements:last').attr('id','sectionQuestion_'+sectionID+'_'+incrementQuestion);
        $(this).parents('.sectionAdd').find('.questionAdd').find('.innerElements:last').find('.questionInput').attr('name','questionName['+sectionID+']['+incrementQuestion+']');

        $(this).parents('.sectionAdd').find('.questionAdd').find('.innerElements:last').find('.questionHintInput').attr('name','questionHint['+sectionID+']['+incrementQuestion+']');
        $(this).parents('.sectionAdd').find('.questionAdd').find('.innerElements:last').find('.questionPlaceholderInput').attr('name','questionPlaceholder['+sectionID+']['+incrementQuestion+']');
        $(this).parents('.sectionAdd').find('.questionAdd').find('.innerElements:last').find('input[type="radio"]').attr('name','answerType['+sectionID+']['+incrementQuestion+']');
    });

    $(document).on('click', '.addSection', function(){
        var sectionID = $('#sectionContent').find('.sectionAdd:last').attr('id');
        var splitSection = sectionID.split('_');
        var incrementSection = parseInt(splitSection[1]) + 1;

        var section = $('#SectionClonePart').find('.sectionAdd').clone();
        section.find('.sectionInput').attr('name','sectionName['+incrementSection+']');
        section.find('.questionInput').attr('name','questionName['+incrementSection+'][1]');
        section.find('.innerElements').attr('id','sectionQuestion_'+incrementSection+'_1');
        section.find('.questionHintInput').attr('name','questionHint['+incrementSection+'][1]');
        section.find('.questionPlaceholderInput').attr('name','questionPlaceholder['+incrementSection+'][1]');
        section.find('.questionPlaceholderInput').attr('name','questionPlaceholder['+incrementSection+'][1]');
        section.find('input[type="radio"]').attr('name','answerType['+incrementSection+'][1]');
        
        $('#sectionContent').append('<hr></hr>');
        $('#sectionContent').append(section);
        $('#sectionContent').find('.sectionAdd:last').attr('id', 'section_'+incrementSection);
    });

    $(document).on('click', '.addQuestionInfo', function(){
        $(this).parents('.innerElements').find('.modal').show();
    });
    $(document).on('click', '[data-dismiss="modal"]', function(){
        $('.modal').hide();
    });

    $(document).on('change', '[name="sizeUnit"]', function() {
        var selectedOpt = $(this).val();
        if(selectedOpt == 'Kids') {
            $(this).parents('.optionElement').find('.kids').show();
            $(this).parents('.optionElement').find('.Regular').hide();
        }

        if(selectedOpt == 'Regular') {
            $(this).parents('.optionElement').find('.kids').hide();
            $(this).parents('.optionElement').find('.Regular').show();
        }
    });

    $(document).on('click', '[type="radio"]', function(){
        $(this).parents('.modal-body').find('.addOptions').html('');
        $(this).parents('.modal-body').find('.answerOption').hide();
        var optionDiv = $('#optionDiv .row:first').clone();

        var sectionQuestion = $(this).parents('.innerElements').attr('id');
        var splitSectionQuestion = sectionQuestion.split('_');
        var sectionID = splitSectionQuestion[1];
        var questionID = splitSectionQuestion[2];

        //var noOfOptions = $(this).parents('.modal-body').find('.optionElement').length;
        
        if($(this).val() == 'Radio'){
            optionDiv.find('.optionInput').attr('name', 'option['+sectionID+']['+questionID+'][1]');
            $(this).parents('.modal-body').find('.addOptions').append(optionDiv);
            $(this).parents('.modal-body').find('.answerOption').show();
            $(this).parents('.modal-body').find('.addOptions').find('.optionElement').attr('id', 'optionElement_'+sectionID+'_'+questionID+'_1');
        }
        if($(this).val() == 'Checkbox'){
            optionDiv.find('.optionInput').attr('name', 'option['+sectionID+']['+questionID+'][1]');
            $(this).parents('.modal-body').find('.addOptions').append(optionDiv);   
            $(this).parents('.modal-body').find('.answerOption').show();
            $(this).parents('.modal-body').find('.addOptions').find('.optionElement').attr('id', 'optionElement_'+sectionID+'_'+questionID+'_1');
        }
        if($(this).val() == 'Dropdown'){
            optionDiv.find('.optionInput').attr('name', 'option['+sectionID+']['+questionID+'][1]');
            $(this).parents('.modal-body').find('.addOptions').append(optionDiv);
            $(this).parents('.modal-body').find('.answerOption').show();
            $(this).parents('.modal-body').find('.addOptions').find('.optionElement').attr('id', 'optionElement_'+sectionID+'_'+questionID+'_1');
        }
        /*if($(this).val() == 'Size') {
            var sizeDiv = $('#sizeOption .row:first').clone();
            $(this).parents('.modal-body').find('.addOptions').append(sizeDiv);   
            $(this).parents('.modal-body').find('.answerOption').show();
            $(this).parents('.modal-body').find('.addOptions').find('.optionElement').attr('id', 'optionElement_'+sectionID+'_'+questionID+'_1');
        }*/
    });

    $(document).on('click', '.addOption', function(){
        var sectionQuestion = $(this).parents('.addOptions').find('.optionElement:last').attr('id');
        var splitSectionQuestion = sectionQuestion.split('_');
        var sectionID = splitSectionQuestion[1];
        var questionID = splitSectionQuestion[2];
        var optionID = parseInt(splitSectionQuestion[3]) + 1;

        var optionDiv = $('#optionDiv .row:first').clone();
        //optionDiv.find('.optionElement').attr('id','optionElement_1_1_1');
        optionDiv.find('.optionInput').attr('name','option['+sectionID+']['+questionID+']['+optionID+']');
        $(this).parents('.modal-body').find('.addOptions').append(optionDiv);
        //$(this).parents('.modal-body').find('.addOptions').find('.addOption').hide();
        //$(this).parents('.modal-body').find('.addOptions .row:first').find('.addOption').show();

        $(this).parents('.modal-body').find('.addOptions').find('.optionElement:last').attr('id','optionElement_'+sectionID+'_'+questionID+'_'+optionID);
    });

    $(document).on('click', '.removeOption', function(){
        var id = $(this).attr("data-rel");
        
        if($(this).parents('.addOptions').find('.optionElement').length > 1){
            if(typeof id !='undefined'){
                var confirm_action = confirm("Are you sure to delete!");
                if (confirm_action == true) {
                    $(this).parents('.optionElement').remove();

                    removeOptionFromDatabase(id);
                }else{
                    return false;
                }
            }else{
                $(this).parents('.optionElement').remove();
            }
        }
    });

    $(document).on('click', '.removeSection', function(){
        var id = $(this).attr("data-rel");
        
        if($(this).parents('#sectionContent').find('.sectionAdd').length > 1){
            if(typeof id !='undefined'){
                var confirm_action = confirm("Are you sure to delete!");
                if (confirm_action == true) {
                    $(this).parents('.innerElements').remove();

                    //removeQuestionFromDatabase(id);
                }else{
                    return false;
                }
            }else{
                $(this).parents('.sectionAdd').remove();
            }
        }
    });

    $(document).on('click', '.removeQuestion', function(){
        var id = $(this).attr("data-rel");
        
        if($(this).parents('.questionAdd').find('.innerElements').length > 1){
            if(typeof id !='undefined'){
                var confirm_action = confirm("Are you sure to delete!");
                if (confirm_action == true) {
                    $(this).parents('.innerElements').remove();

                    removeQuestionFromDatabase(id);
                }else{
                    return false;
                }
            }else{
                $(this).parents('.innerElements').remove();
            }
        }
    });

    function removeQuestionFromDatabase(id) {
        $.ajax({
            headers: {'x-csrf-token': '{{ csrf_token() }}' },
            method: 'DELETE',
            url: "{{ url('') }}/question/"+id,
            //data:{position:data},
            success:function(result){
                console.log(result);
            }
        });
    }

    function removeOptionFromDatabase(id) {
        $.ajax({
            headers: {'x-csrf-token': '{{ csrf_token() }}' },
            method: 'post',
            url: "{{ url('') }}/question/option/delete/"+id,
            //data:{position:data},
            success:function(result){
                console.log(result);
            }
        });
    }
</script>
@endsection



