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
use think\Route;
// 注册路由到index模块的News控制器的read操作
//Route::rule('index/:id','index/index/test');

//Route::rule('news/:id','index/index/news','GET',['id'=>'\d+']);
Route::alias('api','index/api');

return [
    '__pattern__' => [
        'name' => '\w+',
        'id'=>'\d+',
        'classid'=>'\d+',
    ],

    'aboutus/:classid'=>'index/index/aboutus',
    'news/:id'=>'index/index/news',
    'list/:classid'=>'index/index/newslist',
    'search'=>'index/index/newslist',
    // '[hello]'     => [
    //     ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
    //     ':name' => ['index/hello', ['method' => 'post']],
    // ],
];
