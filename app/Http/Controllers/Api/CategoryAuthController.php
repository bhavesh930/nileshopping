<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
use App\Models\Menus;
//use Auth;
use DB;

class CategoryAuthController extends Controller
{
    /**
    * Category List
    */
    public function getCategory(Request $request) {
        try {
            if(!$token = auth('api')->user()) {
                return response()->json(['message' => 'Unauthorized', 'data' => array()], 401);
            }

            $query = Menus::where('menu_id', 2)->where('slug', 'dropdown')->orderBy('sequence');
            if($request->category_id) {
                $query = $query->where('id', $request->category_id);
            }
            
            if(!$request->category_id) {
                $query = $query->whereNull('parent_id');
            }
            
            $categoryList = $query->get();
            if(empty($categoryList)) {
                return response()->json(['message' => 'Empty records', 'data' => array()], 404);   
            }
            $data = array();
            foreach ($categoryList as $key => $value) {
                $subData = array();
                $subCategoryList = Menus::where('menu_id', 2)->where('parent_id', $value->id)->orderBy('sequence')->get();
                if(!empty($subCategoryList)) {
                    foreach ($subCategoryList as $sKey => $sValue) {
                        $sValue->image = ($sValue->image) ? url('/') .'/uploads/menu-icon/'. $sValue->image : '';
                        $categoryQuery = DB::table('listings')->where('menu_id', $sValue->id)->where('status', 3)->whereNull('deleted_at')->get();
                        $sValue->product_count = count($categoryQuery);
                        $subData[] = $sValue;
                    }
                }
                $value->subCategory = $subData;
                $value->image = ($value->image) ? url('/') .'/uploads/menu-icon/'. $value->image : '';
                $data[] = $value;
            }
            return response()->json([
                'message' => 'Category listed successfully',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()."-".$e->getLine()
            ], 500);
        }
    }

}
