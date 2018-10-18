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
Route::post('get-code' , 'Api\User@get_code');
//注册账号
Route::post('register' , 'Api\User@register');
//登录
Route::post('login' , 'Api\User@login');