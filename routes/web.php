<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
/* Index---首页 */
//获取首页图片
Route::get('index/banner' , 'Api\Index@banner_list');

//获取首页滚动广告列表
Route::get('index/advertisement' , 'Api\Index@advertisement_list');


/* User---用户 */
//获取图形验证码
Route::get('img-code' , 'Api\User@img_code');
//获取手机验证码
Route::get('get-code' , 'Api\User@get_code');
//注册账号
Route::get('register' , 'Api\User@register');
//登录
Route::get('login' , 'Api\User@login');
//获取用户基本信息
Route::get('get-userinfo' , 'Api\User@get_userinfo');
//修改密码
Route::get('update-password' , 'Api\User@update_password');
//修改头像
Route::get('upload-img' , 'Api\User@upload_img');

/* Contacts---常用联系人 */
//常用联系人列表
Route::get('contacts-list' , 'Api\Contacts@contacts_list');
//添加常用联系人
Route::get('contacts-add' , 'Api\Contacts@contacts_add');
//删除常用联系人
Route::get('contacts-del' , 'Api\Contacts@contacts_del');

/* Visit --- 影厅 */
//展厅列表
Route::get('visit-list' , 'Api\Visit@visit_list');

//
Route::get('create-order' , 'Api\Order@create_order');

Route::get('alipay/go-pay' , 'Api\Order@go_pay');
Route::post('alipay/notify_url' , 'Api\Order@notify_url');
Route::post('alipay/return_url' , 'Api\Order@return_url');

//订单
Route::get('my-order' , 'Api\User@my_order');

//音频列表
Route::get('audio-list' , 'Api\Audio@audio_list');

//统一下单
Route::get('wxpay/go-pay' , 'Api\Order@order');
Route::post('wxpay/notify-url' , 'Api\Order@wx_notify');

//刷卡支付
Route::get('qrcodepay' , 'Api\Qrpay@pay');