<?php
namespace app\admin\validate;

use think\Validate;

class Featured extends Validate
{
    protected $rule = [
        ['title', 'require'],
        ['image', 'require'],
        ['type', 'require'],
        ['url', 'require'],
        ['description', 'require'],
        //['status', 'number|in:-1,0,1', '状态必须是数字|状态范围不合法'],
        //['listorder', 'number'],
    ];

    //场景设置
    protected $scene = [
        'add' => ['title', 'type', 'url', 'description'],//添加
        //'listorder' => ['id', 'listorder'],//排序
        'status' =>['id','status'],//状态
    ];
}