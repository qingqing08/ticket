<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class Sign extends Controller{

    public function test(){
        $username = Input::get('username');
        $password = Input::get('password');


        $code = rand(100000 , 999999);
        $sign = $this->sign($username , md5($password) , $code);

        $url = "http://api.jinxiaofei.xyz/validate-sign?username=".$username."&password=".$password."&code=".$code."&sign=".$sign;
        return $this->api_return(1 , '获取成功' , $url);
    }
    //
    public function sign($username , $password , $code){
        return md5($username . $password . $code);
    }

    public function validate_sign(){
        $username = Input::get('username');
        $password = md5(Input::get('password'));
        $code = Input::get('code');
        $sign = Input::get('sign');
        if (empty($username)){
            return $this->api_return(2 , '参数错误' , '');
        }
        if (empty($password)){
            return $this->api_return(2 , '参数错误' , '');
        }
        if (empty($code)){
            return $this->api_return(2 , '参数错误' , '');
        }
        if (empty($sign)){
            return $this->api_return(2 , '参数错误' , '');
        }
        $signStr = md5($username . $password . $code);
        if ($sign == $signStr){
            return $this->api_return(1 , '验签成功' , '');
        } else {
            return $this->api_return(0 , '签名错误' , '');
        }
    }

    public static function api_return($status , $msg , $data){
        return ['status' => $status , 'massage' =>  $msg , 'data'   =>  $data];
    }
}
