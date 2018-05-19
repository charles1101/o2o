<?php
namespace app\common\validate;

use think\Validate;

class Bis extends Validate
{
    protected $rule = [
        ['name', 'require|max:50', '商户名必须传递|分类名不能超过50个字符'],
        ['city_id', 'require'],
        ['se_city_id', 'require'],
        ['logo', 'require'],
        ['bank_info', 'require'],
        ['bank_name', 'require'],
        ['bank_user', 'require'],
        ['faren', 'require'],
        ['faren_tel', 'require'],
        ['email', 'email'],

        ['tel', 'require'],
        ['contact', 'require'],
        ['category_id', 'require'],
        ['address', 'require'],
        ['open_time', 'date'],
        ['content', 'require'],

        ['username', 'require', '账户名必须传递'],
        ['password', 'require'],

        ['id', 'number'],
        ['bis_id', 'number'],
        ['status', 'number|in:-1,0,1,2', '状态必须是数字|状态范围不合法'],
        ['is_main', 'number|in:0,1', '状态必须是数字|状态范围不合法'],
    ];
    //场景设置
    protected $scene = [
        'add' => ['name', 'city_id', 'logo', 'bank_info', 'bank_name', 'bank_user', 'faren', 'faren_tel', 'email'],
        'add_location' => ['tel', 'contact', 'category_id', 'address', 'open_time'],
        'add_accout' => ['username', 'password'],
        'status' => ['id', 'bis_id', 'status', 'is_main'],
        'login' => ['username','password'],
    ];
}