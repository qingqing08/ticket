<?php

namespace App\Http\Controllers\Api;

use Illuminate\Console\Scheduling\CommandBuilder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use ValidateCode;

class User extends Controller{
    //

    public function img_code(){
        $phone = Input::get('phone');


        $code = new ValidateCode();
        $code->doimg();

        Redis::set($phone , $code->getCode());
    }

    public function get_code(){
        $phone = Input::get('phone');
        $code = rand(1000 , 9999);
        $callback = Input::get('callback');
        $img_code = Input::get('img_code');

        $redis_code = Redis::get($phone);

        if (empty($phone)){
            return $callback ."(". Commen::Ajax_return('100003' , '参数错误' , '') .")";
        }

        if ($redis_code != $img_code){
//            return $callback . "(" . json_encode([''])
            return $callback ."(". Commen::Ajax_return('100004' , '验证码输入有误' , '') .")";
        }
        $data = [
            'c_type'    =>  $phone,
            'c_code'    =>  $code,
            'c_ctime'   =>  time(),
        ];
        $info = DB::table('code')->where('c_type' , $phone)->first();
        if (empty($info)){
            $result = DB::table('code')->insert($data);
        } else {
            if ($info->c_ctime - time() > 300){
                $result = DB::table('code')->where('c_type' , $phone)->update($data);
            } else {
                $result = true;
                $code = $info->c_code;
            }
        }

        if ($result){
            return $callback ."(". Commen::Ajax_return('100000' , 'Success' , ['code'=>$code]) .")";
        } else {
            return $callback ."(". Commen::Ajax_return('100001' , 'Error' , '') .")";
        }
    }


    public function register(){
        $phone = Input::get('phone');
        $email = Input::get('email');
        $password = Input::get('password');
        $confirm_password = Input::get('confirm_password');
        $code = Input::get('sms_code');
        $callback = Input::get('callback');

        $codeinfo = DB::table('code')->where('c_code' , $code)->first();
        if ($codeinfo->c_type != $phone){
            return $callback . "(" . Commen::Ajax_return('100004' ,'短信验证码输入有误' , '') . ")";
        }
//        echo $code;die;
        $data = [
            'u_phone'   =>  $phone,
            'u_email'   =>  $email,
            'u_password'  =>  md5($password),
            'u_confirmpassword' =>  $confirm_password,
            'u_ctime'   =>  time(),
        ];

        $result = DB::table('user')->insert($data);


    }

    public function login(){
        $phone = Input::get('phone');
        $code = Input::get('code');
        $callback = Input::get('callback');

        $codeinfo = DB::table('code')->where('c_code' , $code)->first();
        if ($codeinfo->c_type != $phone){
            return Commen::Ajax_return($callback , '100004' ,'短信验证码输入有误' , '');
        }

        $data = [
            'u_status'  =>  1,
        ];

        $result = DB::table('user')->where('u_phone' , $phone)->update($data);
        if ($result){
            $userinfo = DB::table('user')->where('u_phone' , $phone);
            return Commen::Ajax_return($callback , '100000' , '登录成功' , $userinfo);
        } else {
            return Commen::Ajax_return($callback , '' , '登录失败' , '');
        }
    }
}
