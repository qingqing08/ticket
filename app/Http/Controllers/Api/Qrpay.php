<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Base;
use Illuminate\Support\Facades\Input;

class Qrpay extends Controller{
    //
    const APPID = 'wx372200381123eeb2';
//    const KEY = 'K8djgYUIQDMCXdr0855kgjdjDGUJDDoi';
    const URL = 'https://api.mch.weixin.qq.com/pay/micropay';
    const MC_ID = '1517545941';
    public function pay(){
        $base = new Base();
        $callback = Input::get('callback');
        $auth_code = Input::get('auth_code');
        $price = Input::get('price');

        $params = [
            'appid' =>  self::APPID,
            'mch_id'    =>  self::MC_ID,
            'nonce_str' =>  uniqid(),
            'body'  =>  '刷卡支付',
            'out_trade_no'  =>  md5(time()),
            'total_fee' =>  $price,
            'spbill_create_ip'  =>  $_SERVER['REMOTE_ADDR'],
            'auth_code' =>  $auth_code,
        ];

        $params = $base->setSign($params);

        $xmldata = $base->ArrToXml($params);

        $resdata = $base->postXml(self::URL, $xmldata);
        $arr = $base->XmlToArr($resdata);

        return $callback . "(" . Commen::Ajax_return('100000' , '支付成功' , json_encode($arr)) . ")";
    }
}
