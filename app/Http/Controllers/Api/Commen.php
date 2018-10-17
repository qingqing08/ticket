<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Commen extends Controller{
    //
    public static function Ajax_return($status , $message , $data){
        return ['status' => $status , 'message' => $message , 'data' => $data];
    }
}
