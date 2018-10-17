<?php

namespace App\Http\Controllers\Api;

use Illuminate\Console\Scheduling\CommandBuilder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class User extends Controller{
    //

    public function get_code(){
        $phone = Input::post('phone');
        $code = rand(1000 , 9999);

        if (empty($phone)){
            return Commen::Ajax_return('100003' , '参数错误' , '');
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
            $result = DB::table('code')->where('c_type' , $phone)->update($data);
        }

        if ($result){
            return Commen::Ajax_return('100000' , 'Success' , ['code'=>$code]);
        } else {
            return Commen::Ajax_return('100001' , 'Error' , '');
        }
    }
    public function register(){
        $phone = Input::post('phone');
        $email = Input::post('email');
        $password = Input::post('password');
        $confirm_password = Input::post('confirm_password');
        $img_code = Input::post('img_code');
        $code = Input::post('code');

        $codeinfo = DB::table('code')->where('c_code' , $code)->first();
        if ($codeinfo->c_type != $phone){
            return Commen::Ajax_return('100004' ,'短信验证码输入有误' , '');
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

        $codeinfo = DB::table('code')->where('c_code' , $code)->first();
        if ($codeinfo->c_type != $phone){
            return Commen::Ajax_return('100004' ,'短信验证码输入有误' , '');
        }

        $data = [
            'u_status'  =>  1,
        ];

        $result = DB::table('user')->where('u_phone' , $phone)->update($data);
        if ($result){
            $userinfo = DB::table('user')->where('u_phone' , $phone);
            return Commen::Ajax_return('100000' , '登录成功' , $userinfo);
        } else {
            return Commen::Ajax_return('' , '登录失败' , '');
        }
    }
}
