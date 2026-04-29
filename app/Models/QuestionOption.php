<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $table = 'question_options';
    public $timestamps = true;

    protected $fillable = ['question_id', 'option'];

    public function Question()
	{
	    return $this->belongsTo('App\Models\Question', 'id');
	}
}
