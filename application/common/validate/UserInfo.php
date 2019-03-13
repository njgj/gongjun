<?php
namespace app\common\validate;

use think\Validate;

class UserInfo extends Validate
{
    //定义验证规则
    protected $rule = [
        'username|用户名' => 'require|chsAlphaNum|min:4|unique:user_info',
        'email|邮箱'     => 'email',
		'realname|姓名' => 'require|min:2',
        'userpwd|密码'  => 'require|length:6,20',
    ];

    //定义验证提示
    protected $message = [
        'username.require' => '请输入用户名',
        'email.email'      => '邮箱格式不正确',
        'userpwd.require' => '密码不能为空',
        'userpwd.length'  => '密码长度6-20位'
    ];

    //定义验证场景
    protected $scene = [
        //登录
        'login'  =>  ['username' => 'require|chsAlphaNum|min:4', 'userpwd'],
        'add'  =>  ['username','realname'],
        'edit' => ['username'=>'require|chsAlphaNum|min:4|unique:user_info,username^userid','realname'],
		'password' => ['userpwd'],
        'myedit' => ['email','realname'],
        
    ];
	
  	
}