<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use DB;

class BrandRequest extends Model
{
    use HasFactory;

    protected $table = 'brandrequests';

    protected $fillable = ['user_id', 'brand_name', 'brand_logo', 'offline_market', 'website_link', 'sell_product_brand', 'mrp_tag', 'brand_owner', 'trademark_doc', 'document_type'];

    protected $dates = [
        'deleted_at'
    ]; 

    public static function brandRequestList($arguments) {
    	$branddata = DB::table('brandrequests')
                        ->whereNull('deleted_at');
        if(isset($arguments['user_role']) && $arguments['user_role'] == 'seller') {
       		$branddata->where('user_id',$arguments['user_id']);
       	}
       	$branddata->orderBy('id', 'desc');
       	if(isset($arguments['limit']) && $arguments['limit']) {
       		$branddata->limit($arguments['limit']);	
       	}
        $result = $branddata->get();

        return $result;
    } 

    public static function brandStatusChange($arguments) {
    	DB::table('brandrequests')->where('id',$arguments['id'])->update(['status'=>$arguments['status'] ]);	
    }  
}
