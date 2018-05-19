<?php
namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        ['username', 'require|max:25', '商户名必须传递|分类名不能超过25个字符'],
        ['email', 'email'],
        ['password', 'require'],
        ['repassword', 'require'],
        ['verifyCode', 'require'],
    ];
    //场景设置
    protected $scene = [
        'register' => ['username', 'email', 'password', 'repassword', 'verifyCode'],
        'login' => ['username', 'password'],
    ];
}