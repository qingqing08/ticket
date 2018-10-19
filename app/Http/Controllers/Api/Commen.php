<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Commen extends Controller{
    //
    public static $key;
    public static function Ajax_return($status , $message , $data){
        return json_encode(['status' => $status , 'message' => $message , 'data' => $data]);
    }
}
