<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';
    public $timestamps = true;

    protected $fillable = ['category_id', 'section', 'question', 'hint', 'placeholder', 'type'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function QuestionOptions()
	{
	    return $this->hasMany('App\Models\QuestionOption', 'question_id');
	}

	public static function getCategoryQuestionData($id)
	{
		$data = array();
        $section = Question::where('category_id',$id)->orderBy('id')->groupBy('section')->get();
        foreach ($section as $key => $value) {
            $questions = Question::where('category_id',$id)->where('section',$value->section)->orderBy('sort')->get();
            $questionArr = array();
            foreach ($questions as $qkey => $question) {
                $questionArr[] = $question;
            }
            $data[] = array('section'=>$value->section, 'question'=>$questionArr);
        }

        return $data;
	}

}
