<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class Seller extends Model
{
    use HasFactory;

    protected $table = 'sellers';

    protected $fillable = ['phone', 'storeEmail', 'storePhone', 'country', 'state', 'city', 'address', 'pincode', 'employeeCode'];

    protected $dates = [
        'deleted_at'
    ];

    public static function listing($arguments) {
    	//print_r($arguments);
    	$listdata = DB::table('listingdatas as ld')
                        ->leftjoin('listings as l', 'ld.listing_id', '=', 'l.id')
                        ->leftjoin('categories as c', 'l.category_id', '=', 'c.id')
                        ->leftjoin('brands as b', 'l.brand_id', '=', 'b.brand_id')
                        ->leftjoin('users as u', 'l.user_id', '=', 'u.id')
                        ->select('ld.*', 'l.category_id', 'l.brand_id', 'l.user_id', 'l.status as lStatus', 'l.unique_id', 'l.created_at as lcreated_at', 'c.name as vertical', 'c.slug as category_slug', 'b.brand_name as brand', 'u.name as username')
                        ->whereNull('l.deleted_at');
        if(isset($arguments['user_id'])) {
        	$listdata->where('user_id', $arguments['user_id']);
        }

       	if(isset($arguments['type']) && $arguments['type'] == 'draft') {
       		$listdata->where('l.status',0)->orWhere('l.status',2);
       	}

       	if(isset($arguments['type']) && $arguments['type'] == 'mylist') {
       		$listdata->where('l.status',3);
       	}

       	if(isset($arguments['type']) && $arguments['type'] == 'archive') {
       		$listdata->where('l.status',1);
       	}

       	//Request for approval from admin
       	if(isset($arguments['type']) && $arguments['type'] == 'qc') {
       		if(isset($arguments['user_role']) && in_array('admin', explode(',', $arguments['user_role'])) ) {
       			$listdata->where(function($q) {
			         $q->where('l.status', 2)
			           ->orWhere('l.status', 3);
			     });
       		} else {
       			$listdata->where('l.status',2);	
       		}
       	}
        $listdata->orderBy('id', 'desc');
		$result = $listdata->get();
		//dd($result);die();

        return $result;
    }

    public static function additionalQuestionViewDashboard($arguments) {
    	$listdata = DB::table('listingadditions as a')
                        ->leftjoin('questions as q', 'a.question_id', '=', 'q.id')
                        ->select('a.*', 'q.question')
                        ->whereNull('a.deleted_at');
        if(isset($arguments['listing_id']) && $arguments['listing_id']) {
       		$listdata->where('a.listing_id',$arguments['listing_id']);
       	}
       	if(isset($arguments['limit']) && $arguments['limit']) {
       		$listdata->limit($arguments['limit']);	
       	}
        $result = $listdata->get();

        return $result;
    }

    public static function listingStatusChange($arguments) {
    	DB::table('listings')->where('unique_id',$arguments['listing_id'])->update(['status'=>$arguments['status'] ]);	
    } 

    public static function listingMenus($arguments) {
    	$menu = DB::table('menus')->where('menu_id',$arguments['menu_id'])->where('slug', 'link')->get();
    	return $menu;
    }
}
