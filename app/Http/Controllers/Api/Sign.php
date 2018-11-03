<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class Sign extends Controller{

    public static function api_return($status , $msg , $data){
        return ['status' => $status , 'massage' =>  $msg , 'data'   =>  $data];
    }

    public static function str_rand($length = 32, $char = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        if(!is_int($length) || $length < 0) {
            return false;
        }

        $string = '';
        for($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }

        return $string;
    }

    public static function sign(){

    }
}
