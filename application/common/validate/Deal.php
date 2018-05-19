<?php
namespace app\common\validate;

use think\Validate;

class Deal extends Validate
{
    protected $rule = [
        ['name', 'require|max:50', '商户名必须传递|分类名不能超过50个字符'],
        ['city_id', 'require'],
        ['se_city_id', 'require'],
        ['category_id', 'require'],
        ['image','require'],
        ['start_time','date'],
        ['end_time','date'],
        ['origin_price','require'],
        ['current_price','require'],
        ['total_count','require'],
        ['coupons_begin_time','require'],
        ['coupons_end_time','require'],
        ['description','require'],
        ['notes','require'],

        ['id', 'number'],
        ['bis_id', 'number'],
        ['status', 'number|in:-1,0,1,2', '状态必须是数字|状态范围不合法'],
    ];
    //场景设置
    protected $scene = [
        'deal_status' => ['id', 'bis_id', 'status'],
        'deal'=>['name', 'city_id','se_city_id','category_id','image','start_time','end_time','total_count','origin_price','current_price','coupons_begin_time','coupons_end_time','description','notes'],
    ];
}