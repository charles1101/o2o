<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function status($status)
{
    if ($status == 1) {
        $str = "<span class='label label-success radius'>正常</span>";
    } elseif ($status == 0) {
        $str = "<span class='label label-danger radius'>待审</span>";
    } elseif ($status == 2) {
        $str = "<span class='label label-danger radius'>不通过</span>";
    } else {
        $str = "<span class='label label-danger radius'>删除</span>";
    }
    return $str;
}

/**
 * 执行cURL
 * @param $curl
 * @param int $type 类型，0表示get，1表示post
 * @param array $data
 */
function doCurl($url, $type = 0, $data = [])
{
    //初始化
    $ch = curl_init();
    //设置cURL传输选项
    //1、CURLOPT_URL：需要获取的 URL 地址，也可以在curl_init() 初始化会话的时候。
    //2、CURLOPT_RETURNTRANSFER：TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出。
    //3、CURLOPT_HEADER：启用时会将头文件的信息作为数据流输出。
    //4、CURLOPT_POST：TRUE 时会发送 POST 请求，类型为：application/x-www-form-urlencoded，是 HTML 表单提交时最常见的一种。
    //5、CURLOPT_POSTFIELDS：全部数据使用HTTP协议中的 "POST" 操作来发送。
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    //post类型
    if ($type == 1) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    //执行cURL并获取内容
    $output = curl_exec($ch);
    //释放cURL句柄
    curl_close($ch);
    return $output;
}

/**
 * 申请结果
 * @param $status
 * @return string
 */
function applyResult($status)
{
    if ($status == 1) {
        $str = '申请成功';
    } elseif ($status == 0) {
        $str = '待审核，审核后平台方会发送邮件通知，请关注邮件';
    } elseif ($status == 2) {
        $str = '非常抱歉，您提交的材料不符合条件，请重新申请';
    } else {
        $str = '该申请已被删除';
    }
    return $str;
}

/**
 * 分页
 * @param $obj
 * @return string
 */
function pagination($obj)
{
    if (!$obj) {
        return '';
    }
    $params = request()->param();
    return "<div class=\"cl pd-5 bg-1 bk-gray mt-20 tp5-o2o\">{$obj->appends($params)->render()}</div>";
}

/**
 * 获取子城市名
 * @param $cityPath
 * @return string
 */
function getSeCityName($cityPath)
{
    if (empty($cityPath)) {
        return '';
    }
    if (preg_match('/,/', $cityPath)) {
        $path = explode(',', $cityPath);
        $seCityId = $path[1];
    } else {
        $seCityId = $cityPath;
    }
    $city = model('City')->get($seCityId);
    return $city->name;
}

/**
 * 获取所属分类的子类
 * @param $categoryPath
 * @return array|string
 */
function getSeCategoryName($categoryPath)
{
    if (preg_match('/,/', $categoryPath)) {
        $path = explode(',', $categoryPath);
        array_shift($path);
        foreach ($path as $seCategoryId) {
            $category = model('Category')->get($seCategoryId);
            $name = $category->name;
            $nameArray[] = $name;
        }
    } else {
        return '';
    }
    return $nameArray;
}

/**
 * 是否是总店
 * @param $isMain
 * @return string
 */
function isMain($isMain)
{
    if ($isMain == 1) {
        $str = "<span>是</span>";
    } else {
        $str = "<span>否</span>";
    }
    return $str;
}

/**
 * 获取门店名称
 * @param $locationIds
 * @return array|bool
 */
function getLocationName($locationIds)
{
    if (empty($locationIds)) {
        return false;
    } else {
        $path = explode(',', $locationIds);
        foreach ($path as $id) {
            $location = model('BisLocation')->get($id);
            $name = $location->name;
            $nameArray[] = $name;
        }
    }
    return $nameArray;
}

/**
 * 获取分店数量
 * @param $ids
 * @return int
 */
function countLocation($ids)
{
    if ($match = preg_match('/,/', $ids)) {
        return count(explode(',', $ids));
    } else {
        return 1;
    }
}

function outTradeNo()
{
    list($a, $b) = explode(' ', microtime());
    $c = explode('.', $a*1000);
    return $c[0].$b.rand(10000,99999);
}


