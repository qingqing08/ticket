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
        $code = new ValidateCode();
        $code->doimg();

        Redis::set('img_code' , $code->getCode());
    }

    public function get_code(){
//        $phone = Input::post('phone');
//        $code = rand(1000 , 9999);
//        $callback = Input::post('callback');

        echo Redis::get('img_code');die;
        if (empty($phone)){
            return Commen::Ajax_return($callback , '100003' , '参数错误' , '');
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
            return Commen::Ajax_return($callback , '100000' , 'Success' , ['code'=>$code]);
        } else {
            return Commen::Ajax_return($callback , '100001' , 'Error' , '');
        }
    }


    public function register(){
        $phone = Input::post('phone');
        $email = Input::post('email');
        $password = Input::post('password');
        $confirm_password = Input::post('confirm_password');
        $img_code = Input::post('img_code');
        $code = Input::post('code');
        $callback = Input::post('callback');

        $codeinfo = DB::table('code')->where('c_code' , $code)->first();
        if ($codeinfo->c_type != $phone){
            return Commen::Ajax_return($callback , '100004' ,'短信验证码输入有误' , '');
        }
//        echo $code;die;
        $data = [
            'u_phone'   =>  $phone,
            'u_email'   =>  $email,
            'u_password'  =>  md5($password),
            'u_confirmpassword' =>  $confirm_password,
            'u_ctime'   =>  time(),
        ];


    }

    public function login(){
        $phone = Input::post('phone');
        $code = Input::post('code');
        $callback = Input::post('callback');

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
