<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Category;
use DB;
use Auth;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('dashboard.question.create', ['categories'  => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->sectionName){
            //Looping Section 
            foreach ($request->sectionName as $skey => $section) {
                //Looping Section Questions
                foreach ($request->questionName[$skey] as $qkey => $question) {
                    $questionModel = new Question();
                    $questionModel->category_id = $request->parent_id;
                    $questionModel->section = $section;
                    $questionModel->question = $question;
                    $questionModel->hint = ($request->questionHint[$skey][$qkey]) ? $request->questionHint[$skey][$qkey] : '';
                    $questionModel->placeholder = ($request->questionPlaceholder[$skey][$qkey]) ? $request->questionPlaceholder[$skey][$qkey] : '';
                    $questionModel->type = $request->answerType[$skey][$qkey];
                    $questionModel->required = ($request->questionRequired[$skey][$qkey]) ? 1 : 0;
                    $questionModel->save();

                    if(($request->answerType[$skey][$qkey] == 'Radio' || $request->answerType[$skey][$qkey] == 'Dropdown' || $request->answerType[$skey][$qkey] == 'Checkbox') && isset($request->option[$skey][$qkey])){
                        //Looping Options
                        foreach ($request->option[$skey][$qkey] as $okey => $option) {
                            $questionOptionModel = new QuestionOption();
                            $questionOptionModel->question_id = $questionModel->id;
                            $questionOptionModel->option = $option;
                            $questionOptionModel->save();
                        }
                    }
                    //
                    Question::where('id',$questionModel->id)->update(['sort'=>$questionModel->id ]);
                }
            }
        }
        
        return redirect('/category/questionList/'.$id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Category id as $id
        $editData = Question::getCategoryQuestionData($id);
        $categories = Category::with('children')->whereNotNull('parent_id')->get();
        return view('dashboard.question.create', [ 'editData' => $editData, 'categories'  => $categories, 'id'=>$id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->sectionName){
            //Looping Section 
            foreach ($request->sectionName as $skey => $section) {
                //Looping Section Questions
                foreach ($request->questionName[$skey] as $qkey => $question) {
                    $questionModel = array(
                                        'category_id' => $id,
                                        'section' => $section,
                                        'question' => $question,
                                        'hint' => ($request->questionHint[$skey][$qkey]) ? $request->questionHint[$skey][$qkey] : '',
                                        'placeholder' => ($request->questionPlaceholder[$skey][$qkey]) ? $request->questionPlaceholder[$skey][$qkey] : '',
                                        'type' => $request->answerType[$skey][$qkey],
                                        'required' => (isset($request->questionRequired[$skey][$qkey])) ? 1 : 0,
                                    );
                    
                    $chkQues = Question::where('category_id',$id)->where('section',$section)->where('id',$qkey)->first();
                    if($chkQues){
                        Question::where('id',$chkQues->id)->update($questionModel);   
                        $questionID = $chkQues->id;
                    }else{
                        $questionID = Question::insertGetId($questionModel);
                    }
                    //$question = Question::updateOrInsert(['category_id' => $id, 'section'=>$section, 'id'=>$qkey],$questionModel);
                    //$qid = DB::getPdo()->lastInsertId();

                    //echo $questionID;die();
                    Question::where('id',$questionID)->whereNull('sort')->update(['sort'=>$questionID]);

                    if(($request->answerType[$skey][$qkey] == 'Radio' || $request->answerType[$skey][$qkey] == 'Dropdown' || $request->answerType[$skey][$qkey] == 'Checkbox') && isset($request->option[$skey][$qkey])){
                        //Looping Options
                        foreach ($request->option[$skey][$qkey] as $okey => $option) {
                            $questionOptionModel = array(
                                                    'question_id' => $questionID,
                                                    'option' => $option,

                                                );

                            $chkQuesOpt = QuestionOption::where('id',$okey)->where('question_id',$questionID)->first();
                            if($chkQuesOpt){
                                QuestionOption::where('id',$chkQuesOpt->id)->update($questionOptionModel);   
                                $optionID = $chkQuesOpt->id;
                            }else{
                                $optionID = QuestionOption::create($questionOptionModel);
                            }
                        }
                    }
                }
            }
        }
        
        return redirect('/category/questionList/'.$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        //this from model function to delete all option for particular question
        $question->QuestionOptions()->delete();

        $question->delete();
    }

    /**
    * Retrive Question for particular Category
    *
    * @param int $id
    * @return \Illuminate\Http\Response
    */
    public function categoryQuestionList($id)
    {
        $data = Question::getCategoryQuestionData($id);
        return view('dashboard.categories.questionList', ['questionData'  => $data, 'id'=>$id]);
    }

    /**
    * Update question sorting.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function questionsort(Request $request)
    {
        if(isset($request->position) && $request->position){
            foreach ($request->position as $key => $value) {
                Question::where('id',$value['question_id'])->update(['sort'=>$value['sort'] ]);
            }
        }
        
    }

    /**
    * Question option Delete
    */
    public function optionDelete($id)
    {
        QuestionOption::where('id',$id)->delete();
    }
}
