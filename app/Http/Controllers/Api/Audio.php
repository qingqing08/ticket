<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Audio extends Controller{
    //
    public function audio_list(){
        $callback = Input::get('callback');

        $list = DB::table('audio')->get();
        $arr = [];
        foreach ($list as $key=>$audio){
            $arr[$key]['src'] = "http://ticketapi.pengqq.xyz/audio/".$audio->music_url;
            $arr[$key]['name'] = $audio->title;
//            $arr[$key]['id'] = $audio->id;
        }
//        dd($list);
        return $callback . "(" . Commen::Ajax_return('100000' , '获取成功' , $arr) . ")";
    }
}
