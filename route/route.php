<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::domain('www','index');

Route::get('tcmspecialty/:major/:major_name/:id', 'index/theme');
/*Route::controller('Index','index/index');*/
Route::get('register','login/register');

Route::post('login','login/login');
Route::post('logout','login/logout');

Route::get('live/:id','live/index');
Route::get('liveDetail/:id','live/liveDetail');
Route::get('livePlay/:liveId/:liveClassId','live/livePlay');

Route::get('online/:id','online/index');
Route::get('onlineDetail/:id','online/onlineDetail');

Route::get('ftf/:id','ftf/index');
Route::get('ftfDetail/:id','ftf/detail');


Route::get('books/:type/:id','books/index');
Route::get('bookDetail/:id','books/bookDetail');
Route::get('examination/majorid/:id','examination/index');
Route::get('ftf/majorid/:id','ftf/index');


return [

];
