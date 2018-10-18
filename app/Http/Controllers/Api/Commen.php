<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Commen extends Controller{
    //
    public static function Ajax_return($callback , $status , $message , $data){
        return $callback . "(".['status' => $status , 'message' => $message , 'data' => $data].")";
    }
}
