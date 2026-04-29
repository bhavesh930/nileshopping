<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'featured', 'menu', 'image'];

    public function parent()
	{
	    return $this->belongsTo('App\Models\Category', 'parent_id');
	}

    public function children()
	{
		return $this->hasMany('App\Models\Category', 'parent_id');
	}

	//mutators
	public function setNameAttribute($value)
	{
	    $this->attributes['name'] = $value;
	    $this->attributes['slug'] = Str::slug($value, '-');
	}

	public static function isVerticalCategory($id)
	{
		$chkSubCategory = Category::where("id",$id)->first();
		if($chkSubCategory->parent_id) {
			$chkParCategory = Category::where("id",$chkSubCategory->parent_id)->first();
			if($chkParCategory->parent_id) {
				return true;
			}
		}

		return false;
	}
}
