<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function uploadImage($image, $folder){
        try{
        	$this->createDirectoryMultiple($folder);
	        $imagename = time().rand('10000','99990').'.'.$image->getClientOriginalExtension();
	        $destinationPath = public_path($folder);
	        $image->move($destinationPath, $imagename);
	        return $imagename;
        }
        catch(\Exception $e){
            throw $e;
        }
    }

    public static function createDirectoryMultiple($pathInfo){
        $splitPath = explode('/', $pathInfo);
        $splitPath = array_filter($splitPath);
        if(!count($splitPath) > 0){
            return false;
        }

        $initialPath = '/';

        foreach($splitPath as $paths){
            $initialPath = $initialPath.$paths.'/';
            if ( !file_exists(public_path($initialPath)) ) {
                mkdir(public_path($initialPath), 0777);
                chmod(public_path($initialPath), 0777);//Set Permission
            }
        }
        return true;
    }
}
