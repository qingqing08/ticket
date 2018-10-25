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

    //图形验证码
    public function img_code(){
        $phone = Input::get('phone');


        $code = new ValidateCode();
        $code->doimg();

        Redis::set($phone , $code->getCode());
    }

    //发送短息验证码
    public function get_code(){
        $phone = Input::get('phone');
        $code = rand(1000 , 9999);
        $callback = Input::get('callback');
        $img_code = Input::get('img_code');

        $redis_code = Redis::get($phone);

        if (empty($phone)){
            return $callback ."(". Commen::Ajax_return('100002' , 'Error' , '') .")";
        }

        if ($redis_code != $img_code){
//            return $callback . "(" . json_encode([''])
            return $callback ."(". Commen::Ajax_return('100003' , 'Error' , '') .")";
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
            if (time() - $info->c_ctime > 300){
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


    //注册
    public function register(){
        $phone = Input::get('phone');
        $email = Input::get('email');
        $password = Input::get('password');
        $confirm_password = Input::get('confirm_password');
        $code = Input::get('sms_code');
        $callback = Input::get('callback');

        $codeinfo = DB::table('code')->where('c_code' , $code)->first();
        if ($codeinfo->c_type != $phone){
            return $callback . "(" . Commen::Ajax_return('100004' ,'Error' , '') . ")";
        }
        $user_info = DB::table('user')->where('u_phone')->first();
        if (!empty($user_info)){
            return $callback . "(" . Commen::Ajax_return('100005' , 'Error' , '') . ")";
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
        if($result){
            return $callback . "(" . Commen::Ajax_return('100000' , '注册成功' , '') . ")";
        } else {
            return $callback . "(" . Commen::Ajax_return('100001' , 'Error' , '') . ")";
        }

    }

    //登录
    public function login(){
        $phone = Input::get('phone');
        $password = Input::get('password');
        $img_code = Input::get('img_code');
        $callback = Input::get('callback');

        $user_info = DB::table('user')->where(['u_phone'=>$phone])->first();
        if (empty($user_info)){
            return $callback . "(" . Commen::Ajax_return('100006' , '用户不存在' , '') . ")";
        }

        if ($user_info->u_password != md5($password)){
            return $callback . "(" . Commen::Ajax_return('100007' , '密码错误' , '') . ")";
        }
        $data = [
            'u_status'  =>  1,
            'u_token'   =>  md5($phone.$password.time()).$user_info->u_id,
        ];
        $redis_code = Redis::get($phone);

        $result = DB::table('user')->where('u_phone' , $phone)->update($data);
        if ($result){
            $user_info = DB::table('user')->select('u_id' , 'u_username' , 'u_headimg' , 'u_token' , 'u_phone')->where(['u_phone'=>$phone , 'u_password'=>md5($password)])->first();
            return $callback . "(" .Commen::Ajax_return('100000' , '登录成功' , $user_info) . ")";
        } else {
            return $callback . "(" .Commen::Ajax_return('100001' , 'Error' , '') . ")";
        }
    }

    //用户基本信息
    public function get_userinfo(){
        $user_id = Input::get('user_id');
        $token = Input::get('token');
        $callback = Input::get('callback');

        if (empty($token)){
            return $callback . "(" . Commen::Ajax_return('100002' , 'Error' , '') . ")";
        }
        $userinfo = DB::table('user')->select('u_phone' , 'u_email' , 'u_username' , 'u_headimg')->where(['u_id'=>$user_id , 'u_token'=>$token])->first();

        if (empty($userinfo)){
            return $callback . "(" . Commen::Ajax_return('100006' , 'Error' , '') . ")";
        } else {
            return $callback . "(" . Commen::Ajax_return('100000' , '获取成功' , $userinfo) . ")";
        }
    }

    //修改密码
    public function update_password(){
        $user_id = Input::get('user_id');
        $old_password = Input::get('old_password');
        $new_password = Input::get('new_password');
        $token = Input::get('token');
        $callback = Input::get('callback');

        if (empty($user_id)){
            return $callback . "(" . Commen::Ajax_return('100002' , 'Error' , '') . ")";
        }
        $userinfo = DB::table('user')->where(['u_id'=>$user_id , 'u_password'  =>   md5($old_password) , 'u_token'=>$token])->first();
        if (empty($userinfo)){
            return $callback . "(" . Commen::Ajax_return('100006' , 'Error' , '') . ")";
        } else {
            $data = [
                'u_password'    =>  md5($new_password),
                'u_confirmpassword' =>  $new_password,
            ];

            $result = DB::table('user')->where('u_id' , $user_id)->update($data);
        }

        if ($result){
            return $callback . "(" . Commen::Ajax_return('100000' , '修改成功' , '') . ")";
        } else {
            return $callback . "(" . Commen::Ajax_return('100001' , 'Error' , '') . ")";
        }
    }

    //上传头像
    public function upload_img(){
        $user_id = Input::get('user_id');
        $path = Input::get('path');
        $callback = Input::get('callback');
        $token = Input::get('token');

//        file_put_contents('./'.$user_id.'.jpg' , $path);

        $user_info = DB::table('user')->where(['u_id'=>$user_id , 'u_token'=>$token])->first();

        if (empty($user_info)){
            return $callback . "(" . Commen::Ajax_return('100002' , 'Error' , '') . ")";
        }
        $data = [
            'u_headimg' =>  $path,
        ];

        $result = DB::table('user')->where('u_id' , $user_id)->update($data);

        if ($result){
            return $callback . "(" . Commen::Ajax_return('100000' , '上传成功' , '') . ")";
        } else {
            return $callback . "(" . Commen::Ajax_return('100001' , 'Error' , '') . ")";
        }

    }
}
