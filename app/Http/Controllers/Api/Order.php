<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use RSA;
use Base;

class Order extends Controller{
    //
    public function create_order(){
        $user_id = Input::get('user_id');
        $token = Input::get('token');
        $count = Input::get('count');
        $venueId = Input::get('venueId');
        $ticketId = Input::get('ticketId');
        $inData = Input::get('inDate');
        $order_id = date('YmdHis').rand('100' , '999').$venueId;
        $callback = Input::get('callback');
        $sum_price = 0;
        for ($i=0;$i<=$count-1;$i++){
            $data[$i]['co_type'] = Input::get('cardType'.$i);
            $data[$i]['co_num'] = Input::get('cardNo'.$i);
            $data[$i]['co_name'] = Input::get('name'.$i);
            $data[$i]['v_price'] = trim(Input::get('price'.$i) , '¥');
            $data[$i]['co_id'] = Input::get('contactId'.$i);
            $sum_price = trim(Input::get('price'.$i) , '¥')+$sum_price;
        }
//        echo $sum_price;
//        dd($data);

        $visit = DB::table('visit')->where('v_id' , $venueId)->first();

        $order_data = [
            'o_orderid' =>  $order_id,
            'o_content' =>  $visit->v_name,
            'o_price'   =>  $sum_price,
            'o_num'     =>  $count,
            'o_inData'  =>  $inData,
            'o_ctime'   =>  time(),
        ];

        foreach ($data as $v){
            $order_content = [
                'u_id'  =>  $user_id,
                'o_orderid' =>  $order_id,
                'v_name'    =>  $visit->v_name,
                'co_id'     =>  $v['co_id'],
                'v_price'   =>  $v['v_price'],
                'oc_ctime'  =>  time(),
                'co_name'    =>  $v['co_name'],
                'co_num'    =>  $v['co_num'],
                't_id'      =>  $ticketId,
            ];

            $res = DB::table('order_content')->insert($order_content);
        }
        if ($res){
            $result = DB::table('order')->insert($order_data);
            if ($result){
                return $callback . "(" . Commen::Ajax_return('100000' , '下单成功' , ['order_id'=>$order_id , 'price'=>$sum_price]) . ")";
            } else {
                return $callback . "(" . Commen::Ajax_return('100001' , 'Error' , '') . ")";
            }
        } else {
            return $callback . "(" . Commen::Ajax_return('100008' , '下单失败' , '') . ")";
        }

    }

    public function go_pay(){
//        dd(Input::post());die;
        $order_id = Input::get('order_id');
        $price = Input::get('price');
        $callback = Input::get('callback');
        $pri_key = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCHhWtG4PWu9mn/l5jainCiK3OnkUSLr68dqficur96Ajr3DyMGs7v6bG/GbPudNzREio9WMqYmbimvjtcx7H4dUy+g+B9qLTh90S3aOauW2EUs0t8NJ7KZA1/KiAMA4TngGTAyDREx4b1TOGOF3Ln2hlWXXRM7tMvR+B2hqAf/K3CdFAQO2Q8uiw9pnKHOfiZO642h5SIozTe77s0Gg85NSuaQ1G4CSAvxc2IIBQ/tofZtwLrRpa/j5SwgljekZw9J6kn/JpF/dVAofHhToabPyfecPX3ym2a1ek9Jpgm3mh8JfmYnUlkMRsxy4HeNe51dP1KwKnjGFt31Rb/XY5ZRAgMBAAECggEACdhWb8K99mTuVGQV9aJjBlTzxPOXsDImHZiQeApVCK8Ky5Hs8Hq0KEAiap7WNJijEmuieBeb3GTaYGeXGIherRCzABWmapc4aGN+2kCgR4gUlmoHTDRbFCSbm+H/ndu+0Zni12/9lMsabuZEzJ+5XsBpjWJ0mDzNJcbJmEnVsuLAWt7sjmo10JJH3dGm2HJ4byztEWuHTlHevMBVftafabagM88Ioi4Y1SvWf4OJifrI9bq1TMGWcgEj2Ac5iuKKwViR1FRekcsh9Zs/PeBUBQzInUGs+bUMDaO9mpDewHFdtZ58HT6rpWWBtVnztoM79eX2Bx9Alyze9FK+B+TucQKBgQDk5IS71RJ2WIpnURpnec12As1HY8jTH2bqvs36th9YbJsEYioqZe+jBskOjE5bUR8jQLgRDppeK/03s1ERT59Nr5irz18Ed39eZVv/WpoGu7oyeJUYZbfVNw5XD2VKXXR3w1ITOrWpotmnsDVc/Ai7Yjs5xO1iDYCK06byY/4k1QKBgQCXkhlXUR/loa3TsrGeEbqyQInNPbO9iuwvfUJA2ziERSCyrgi277QkIVBHXbVAOY5xSVW221q6G7nT/o21dirEPlqyFkge9GeFNtRJz0YBHP9+6ckTlX2vyIc30jxgAC7kyb0IrqBmtfymkcF3/u2B1Q72gGHUSrW4NXpTyc6ZjQKBgD0M1npi8nGuW/wCndBLpIl9ZdNMwhvNnF2wVrAwM1waW55nsGdumOQawzWmJqAkmvGEKZQjGPlVMkzQ/yZm3k6SL15kCSvf05ER59/MApkZKSidEOdY+hdcf+6opJOZKZ9n8VQ/rIR6cyNO1GzgrFOOd82IwOgOQeLFYRn1oauhAoGBAIXSAfmrsGPHuXc9P8B1msYiYQgKQBVLAHh1OPeWFXICrnnTWfJZ9Ewp9Xzs6UgJCRBQVRMa3CGQtSLMjkT2TY/yFZVCQu7Bjlx5Kjj4fbAh8BoXQua9h7iZbXkFbzS7NKveyb1OoGPOrYBLE+tj8kI83/cXJkiOpZ476QLtHDFJAoGAStKZT6IE26vY58HtbMmU3hAUraOI5WDqunj/vc+q43vAPSlhKHOJ7P4JDu7HpPJKWUxCs+X3lihdDc3SGCGNV6Ynfc4UyUZyqvse47M/zn+GngYFNhO7dBonet03aZOnTz9kh0LCJ3ZbuxUJnzcvdQtP/l501Z1GNSxDzgu0NZo=';
        $pub_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0/hl6rBbL5J3oqkfQrCZ+C3eoROrtv/yKlDQP/24NrpZ0w4iPyuUrWANGk5TszBvdGO4GCCq2aWJj1Qn6G0yuSZZIk/aL8YwSC81y8tjEYDKhTu3Zv6doQRXqcq/oLKJzwuKiVmlWu20Gg+fByb/MnQAmj7GuVXsdXPU5kX2Bb/OOmoj1OwgaNLHQs0rgf98V7V5STinH3/FXeN9UTUN+Jc86Fq0VbDcSPs4o0ePr+MbGXYqllUNbQGaKTUD7zfuOn78MwO9CCiKdHWHMvCZOiGS9z+rq+yy8BuLg+nlxej7taZAFJ10eZQKpigJzT8qgPELqLChTpykYdKtfG1D3QIDAQAB';

//        include './Rsa.php';

        //公共请求参数
        $arr = [
            'app_id' => '2018022702283497',
            'method' => 'alipay.trade.app.pay',
//            'return_url' => 'http://ticketapi.pengqq.xyz/alipay/return_url', //同步地址
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'notify_url' => 'http://ticketapi.pengqq.xyz/alipay/notify_url',
            'biz_content' => '',
        ];

//业务请求参数
        $arr_params = [
            'subject'=>'测试',
            'out_trade_no'=> $order_id,
            'total_amount'=> 0.01,
            'product_code'=>'QUICK_MSECURITY_PAY'
        ];

        $arr['biz_content'] = json_encode($arr_params,JSON_UNESCAPED_UNICODE);
//echo '<pre>';
        ksort($arr);
        $str =  urldecode(http_build_query($arr));

        $rsa = new RSA();
//
        $arr['sign'] =  $rsa->rsaSign($str, $pri_key);
//        $str =  urldecode(http_build_query($arr));
        return $callback . "(" . Commen::Ajax_return('100000' , '获取成功' , ['signstr'=>http_build_query($arr)]) . ")";
////dd($arr);die;
////        return redirect('http://openapi.alipay.com/gateway.do?' . http_build_query($arr));
//        header('location:https://openapi.alipay.com/gateway.do?' . http_build_query($arr));
    }

    public function notify_url(){
        $params = Input::post();
        $pub_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0/hl6rBbL5J3oqkfQrCZ+C3eoROrtv/yKlDQP/24NrpZ0w4iPyuUrWANGk5TszBvdGO4GCCq2aWJj1Qn6G0yuSZZIk/aL8YwSC81y8tjEYDKhTu3Zv6doQRXqcq/oLKJzwuKiVmlWu20Gg+fByb/MnQAmj7GuVXsdXPU5kX2Bb/OOmoj1OwgaNLHQs0rgf98V7V5STinH3/FXeN9UTUN+Jc86Fq0VbDcSPs4o0ePr+MbGXYqllUNbQGaKTUD7zfuOn78MwO9CCiKdHWHMvCZOiGS9z+rq+yy8BuLg+nlxej7taZAFJ10eZQKpigJzT8qgPELqLChTpykYdKtfG1D3QIDAQAB';
        $json = json_encode($params);
//        $json = '{"gmt_create":"2018-10-26 16:43:16","charset":"utf-8","seller_email":"68063160@qq.com","subject":"\u6d4b\u8bd5","sign":"vdiGgvrZ6uNFa7vhwIeYltFgBHtq1TT117w8pO9XmAvWCNynnCR4lf8VTUjF\/235ylB\/msli3uibVVF7L3qy4vKJvnGIYq+pcfRn7Ceb3hOwmfYMISBSie2KDO\/PynCPl\/FxWBd2nbaL3rrpSeuFhy506hBn7i4TeGelc4JOPBI34ID\/WZgyOc6U9+nKH0bjARA3omSPydn6rqkdCifTBI1UKGHag7WaX\/jAIoYMYqWc9r4PVJl9y0BCU8WGs0y7gg\/oleb8CJaVcx\/sakSXujS2brhLgTUEYZpjNpX33v33wTb8DXXKAyLot7VfwmKSEVd+r2pSHQvD4g\/tCK2RbQ==","buyer_id":"2088312755292251","invoice_amount":"0.01","notify_id":"2018102600222164317092251022953316","fund_bill_list":"[{\"amount\":\"0.01\",\"fundChannel\":\"PCREDIT\"}]","notify_type":"trade_status_sync","trade_status":"TRADE_SUCCESS","receipt_amount":"0.01","app_id":"2018022702283497","buyer_pay_amount":"0.01","sign_type":"RSA2","seller_id":"2088521397903003","gmt_payment":"2018-10-26 16:43:17","notify_time":"2018-10-26 16:43:17","version":"1.0","out_trade_no":"201810260843067502","total_amount":"0.01","trade_no":"2018102622001492251008952036","auth_app_id":"2018022702283497","buyer_logon_id":"139***@qq.com","point_amount":"0.00"}';
        file_put_contents('./alipay.log' , $json. "\r\n" ,FILE_APPEND);
        $params = json_decode($json , true);
        $sign = $params['sign'];
        unset($params['sign']);
        unset($params['sign_type']);
        ksort($params);
        $str = urldecode(http_build_query($params));
        $rsa = new RSA();
        $stat = $rsa->rsaCheck($str , $pub_key , $sign);
        if ($stat){
            file_put_contents('./alipay.log','签名验证通过'. "\r\n" ,FILE_APPEND);
            if ($params['trade_status'] == 'TRADE_SUCCESS' || $params['trade_status'] == 'TRADE_FINISHED'){
                file_put_contents('./alipay.log','支付成功'. "\r\n" ,FILE_APPEND);
                $order_info = DB::table('order')->where('o_orderid',$params['out_trade_no'])->first();
//                if ($params['total_amount'] == $order_info->price){
                if ($params['total_amount'] == 0.01){
                    file_put_contents('./alipay.log','判断金额成功'. "\r\n" ,FILE_APPEND);
                    $data['oc_paystatus'] = 1;
                    $order_data['o_status'] = 2;
                    $order_data['o_paystatus'] = 1;
                    DB::table('order')->where(['o_orderid'=>$params['out_trade_no'] , 'o_status'=>1])->update($order_data);
                    $res = DB::table('order_content')->where(['o_orderid'=>$params['out_trade_no'] , 'oc_paystatus'=>0])->update($data);
                    if ($res){
                        file_put_contents('./alipay.log','修改订单状态成功'. "\r\n" ,FILE_APPEND);
                        echo "success";
                    } else {
                        file_put_contents('./alipay.log','修改订单状态失败'. "\r\n" ,FILE_APPEND);
                    }
                }
            }
        } else {
            file_put_contents('./alipay.log','签名验证不通过'. "\r\n" ,FILE_APPEND);
        }

    }

    public function return_url(){
        $arr = Input::all();
        $json = json_encode($arr);
        file_put_contents('./return.txt' , $json);
//        $order_info = DB::table('ordercontent')->where('order_number',$arr['out_trade_no'])->first();
//        if ($arr['total_amount'] == $order_info->price){
//            $content = "支付成功,请等待发货";
//        }
//
//        return view('return' , ['content'=>$content]);
        dd($arr);
    }

//    const UNIFIEDORDER = "https://api.mch.weixin.qq.com/pay/unifiedorder";
//    const NOTIFY_URL = "http://ticketapi.pengqq.xyz/wxpay/notify-url";
    public function order(){
        $order_id = Input::get('order_id');
        $price = Input::get('price');
        $callback = Input::get('callback');
        $base = new Base();

        $arr = $base->unifiedOrder($order_id);

        if ($arr['return_code'] == 'SUCCESS') {
            $return_arr = [
                'appid' =>  'wx372200381123eeb2',
                'partnerid'      =>  '1517545941',
                'prepayid'      =>  $arr['prepay_id'],
                'package'      =>  'Sign=WXPay',
                'noncestr'      =>  uniqid(),
                'timestamp'     =>  time(),
            ];

            $return_arr['sign'] = $base->getSign($return_arr);
//            $return_arr = $base->setSign($return_arr);

            $json = json_encode($return_arr);

            return $callback . "(" . json_encode(['paySignStr'=>$json]) . ")";
        }
    }

    public function wx_notify(){
//        echo "1";
        $data = file_get_contents('php://input');
        file_put_contents('./wx_notify.log' , $data."\r\n" , FILE_APPEND);
        $base = new Base();
        $returnParams = [
            'return_code' => 'SUCCESS',
            'return_msg'  => 'OK'
        ];
        echo $base->ArrToXml($returnParams);
    }
}
