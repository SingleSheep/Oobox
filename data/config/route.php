<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

/**
 * Index
 */
Route::rule('/','index/index/index');
Route::rule('verifycode','index/index/verifycode');
Route::rule('Search/','index/search/index');
Route::rule('Help/','index/Help/index');
Route::rule('Check/name','index/Entry/checkname'); //AJAX
Route::rule('Check/mail','index/Entry/checkmail');  //AJAX

/**
 * User
 */
Route::rule('User/reset_password','index/Entry/reset_password');
Route::rule('User/lost_username','index/Entry/lost_username');
Route::rule('User/action','index/Active/actionUser');
Route::rule('User/active','index/Active/activationCode');
Route::rule('User/active/remail','index/Active/remail');
Route::rule('User/login','index/Entry/login');
Route::rule('User/verifyCode','index/Entry/verifyCode');
Route::rule('User/register','index/Entry/register');
Route::rule('User/logout','index/Ucenter/logout');
Route::rule('User/edit/:name','index/Ucenter/changeInfo');
Route::rule('User/pwd/:name','index/Ucenter/changePwd');
Route::rule('User/avatar/:name','index/Ucenter/changeAvatar');
Route::rule('User/settings','index/Ucenter/settings');
Route::rule('User/dashboard','index/Ucenter/dashboard');
Route::rule('User/:name','index/Ucenter/index'); //查看主页