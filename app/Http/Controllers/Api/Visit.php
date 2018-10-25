<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class Visit extends Controller{
    //
    public function visit_list(){
        $callback = Input::get('callback');
        $list = DB::table('visit')->select('v_id' , 'v_type' , 'v_name' , 'v_count')->get();

        return $callback . "(" . Commen::Ajax_return('100000' , '获取成功' , $list) . ")";
    }
}
