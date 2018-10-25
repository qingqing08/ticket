<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Index extends Controller{
    //
    //首页图片
    public function banner_list(){
        $callback = Input::get('callback');
        $banner_list = DB::table('banner')->get();

        return $callback . "(" . Commen::Ajax_return('100000' , '获取成功' , $banner_list) . ")";
    }

    //滚动广告
    public function advertisement_list(){
        $advertisement_list = DB::table('advertisement')->select('a_id' , 'a_title')->get();

        return Commen::Ajax_return('100000' , '获取成功' , $advertisement_list);
    }

    //是否登录
    public function is_login(){
        
    }
}
