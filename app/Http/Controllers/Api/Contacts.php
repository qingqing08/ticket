<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Contacts extends Controller{
    //
    public function contacts_list(){
        $user_id = Input::get('user_id');
        $callback = Input::get('callback');
        $token = Input::get('token');

        $user_info = DB::table('user')->where(['u_id'=>$user_id , 'u_token'=>$token])->first();
        if (empty($user_info)){
            return $callback . "(" . Commen::Ajax_return('100002' , 'Error' , '') . ")";
        }
        $list = DB::table('contacts')->select('co_id' , 'co_name' , 'co_num' , 'co_type')->where('u_id' , $user_id)->get();

        return $callback . "(" . Commen::Ajax_return('100000' , '获取成功' , $list) .")";
    }

    public function contacts_add(){
        $user_id = Input::get('user_id');
        $callback = Input::get('callback');
        $name = Input::get('name');
        $type = Input::get('type');
        $num = Input::get('num');
        $token = Input::get('token');

        $user_info = DB::table('user')->where(['u_id'=>$user_id , 'u_token'=>$token])->first();
        if (empty($user_info)){
            return $callback . "(" . Commen::Ajax_return('100002' , 'Error' , '') . ")";
        }
        $data = [
            'u_id'  =>  $user_id,
            'co_name'   =>  $name,
            'co_type'   =>  $type,
            'co_num'    =>  $num,
            'co_ctime'  =>  time(),
        ];

        $result = DB::table('contacts')->insert($data);
        if ($result){
            return $callback . "(" . Commen::Ajax_return('100000' , '添加成功' , '') . ")";
        } else {
            return $callback . "(" . Commen::Ajax_return('100001' , 'Error' , '') . ")";
        }
    }

    public function contacts_del(){
        $user_id = Input::get('user_id');
        $token = Input::get('token');
        $data_id = Input::get('data_id');
        $callback = Input::get('callback');

        $user_info = DB::table('user')->where(['u_id'=>$user_id , 'u_token'=>$token])->first();
        if (empty($user_info)){
            return $callback . "(" . Commen::Ajax_return('100002' , 'Error' , '') . ")";
        }

        $result = DB::table('contacts')->where('co_id' , $data_id)->delete();
        if ($result){
            return $callback . "(" . Commen::Ajax_return('100000' , '删除成功' , '') . ")";
        } else {
            return $callback . "(" . Commen::Ajax_return('100001' , 'Error' , '') . ")";
        }
    }
}
