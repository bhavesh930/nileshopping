@extends('dashboard.base')
<style type="text/css">
    .sectionName{
        background: lightgrey;
        padding: 5px;
        margin-bottom: 10px;
    }
    .sectionName > li{
        list-style: none;
    }
    .sectionName > li > strong{
        padding: 5px;color: black;font-weight: bold;margin-bottom: 0;
    }
</style>
@section('content')

        <div class="container-fluid">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="card">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-4 ml-1">
                                <a href="{{ url('') }}/question/{{$id}}/edit">
                                    <button class="btn btn-primary" data-dismiss="modal">Edit</button>
                                </a>
                            </div>
                            <div class="card-body">
                            @if($questionData)
                                <?php //echo json_encode($questionData);?>
                                @foreach($questionData as $key => $value)
                                    <div class="sectionName">
                                        <li id="{{$value['section']}}"><strong>{{ $value['section'] }}</strong>
                                            <table class="table table-bordered" id="html_table" width="100%" style="margin-bottom: 0;">
                                                <tbody id="questionTbody_{{ $value['section'] }}">
                                                    @foreach(json_decode(json_encode($value['question'])) as $qkey => $question)
                                                    <tr id="{{ $question->id }}" sort="{{ $question->sort }}">
                                                        <td>
                                                            {{ $question->question }} <strong>({{ $question->type }})</strong>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </li>
                                    </div>
                                @endforeach
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection


@section('javascript')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    $(function () {
        /*$('tbody[id^=questionTbody]').sortable({
            stop: function() {
                var selectedData = new Array();
                $("tr").each(function() {
                    selectedData.push({question:$(this).attr("id"), type:'question'});
                });
                console.log(selectedData);
                //updateQuestionSequence(selectedData);
            }
        });*/   

        $('tbody[id^=questionTbody]').sortable({
            start: function(e, ui) {
                var oldView = $.map($(this).find('tr'), function(el, i) {
                    return el.id;
                });

                var sortArray = new Array();
                $(this).find('tr').each(function() {
                    if($(this).attr("sort")){
                        sortArray.push($(this).attr("sort"));    
                    }
                });
                //console.log('old IDS'+ oldView);
                //console.log('old sort'+ sortArray);

                $(this).attr('data-previndex', oldView);
                $(this).attr('data-sort', sortArray);
            },

            stop: function(e, ui) { 
                var selectedData = new Array();
                var newSortArray = new Array();
                $(this).find('tr').each(function() {
                    newSortArray.push($(this).attr("sort"));
                });

                var newValue = $.map($(this).find('tr'), function(el, i) {
                    return el.id;
                });

                var oldIndex = $(this).attr('data-previndex');
                var oldArr = oldIndex.split(',');

                var sortIndex = $(this).attr('data-sort');
                var sortArr = sortIndex.split(',');
                //console.log('new IDS'+ newValue);
                //console.log('new sort'+ newSortArray);

                $.map(newValue, function(val, i) {//console.log(oldArr);
                     //return 'new value ' + val + ' Old value '+ oldArr[i];

                    selectedData.push({sort:sortArr[i], question_id:val});

                    $("tbody").find('tr#'+val).attr('sort',sortArr[i]);
                });
                //console.log(selectedData);

                updateQuestionSequence(selectedData,"section");
            }
        });

        function updateQuestionSequence(data) {
            $.ajax({
                headers: {'x-csrf-token': '{{ csrf_token() }}' },
                method: 'POST',
                url: "{{ url('') }}/question/sort",
                data:{position:data},
                success:function(result){
                    console.log(result);
                    //alert('your change successfully saved');
                }
            });
        }
    })
</script>
@endsection

