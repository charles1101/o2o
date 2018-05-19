<?php
namespace app\admin\controller;

use think\Controller;

class Deal extends Controller
{
    public function index()
    {
        $data = input('get.');
        $sdata = [];
        if (!empty($data['start_time']) && !empty($data['end_time']) && strtotime($data['start_time']) < strtotime($data['end_time'])) {
            $sdata['create_time'] = [
                ['gt', strtotime($data['start_time'])],
                ['lt', strtotime($data['end_time'])],
            ];
        }
        if (!empty($data['category_id'])) {
            $sdata['category_id'] = $data['category_id'];
        }
        if (!empty($data['city_id'])) {
            $sdata['city_id'] = $data['city_id'];
        }
        if (!empty($data['name'])) {
            $sdata['name'] = ['like', '%' . $data['name'] . '%'];
        }
        $deal = model('Deal')->getNormalDeals($sdata);
        //获取一级城市
        $citys = model('City')->getNormalCitysByParentId();
        $cityArr = [];
        foreach ($citys as $city) {
            $cityArr[$city->id] = $city->name;
        }
        //获取一级分类
        $categorys = model('Category')->getNormalCategorys();
        $categoryArr = [];
        foreach ($categorys as $category) {
            $categoryArr[$category->id] = $category->name;
        }
        return $this->fetch('', [
            'deal' => $deal,
            'citys' => $citys,
            'categorys' => $categorys,
            'category_id' => empty($data['category_id']) ? '' : $data['category_id'],
            'city_id' => empty($data['city_id']) ? '' : $data['city_id'],
            'start_time' => empty($data['start_time']) ? '' : $data['start_time'],
            'end_time' => empty($data['end_time']) ? '' : $data['end_time'],
            'name' => empty($data['name']) ? '' : $data['name'],
            'cityArr' => $cityArr,
            'categoryArr' => $categoryArr,
        ]);
    }

    public function apply()
    {
        $deal = model('Deal')->getDealByStatus(0);
        return $this->fetch('', [
            'deal' => $deal
        ]);
    }

    public function detail()
    {
        $id = input('get.id');
        if (empty($id)) {
            return $this->error('id错误');
        }
        $dealData = model('Deal')->get($id);
        $bisId = input('get.bis_id');
        //获取一级城市
        $citys = model('City')->getNormalCitysByParentId();
        //获取一级分类
        $categorys = model('Category')->getNormalCategorys();
        $bisLocations = model('BisLocation')->getLocationByBisId($bisId);
        $seCategoryName = getSeCategoryName($dealData['category_path']);
        $seCityName = getSeCityName($dealData['se_city_id']);
        $locationName = getLocationName($dealData['location_ids']);
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
            'bisLocations' => $bisLocations,
            'seCategoryName' => $seCategoryName,
            'seCityName'=>$seCityName,
            'locationName' => $locationName,
            'dealData' => $dealData,
        ]);
    }

    /**
     * 修改状态
     */
    public function status()
    {
        $data = input('get.');
        $validate = validate('deal');
        if (!$validate->scene('deal_status')->check($data)) {
            $this->error($validate->getError());
        }
        $res = model('Deal')->save(['status' => $data['status']], ['id' => $data['id']]);
        if ($res) {
            $bisId = model('Deal')->get($data['id'])->bis_id;
            $bis = model('Bis')->get($bisId);
            $to = $bis->email;
            $title = 'o2o团购商品审核结果通知';
            $url = request()->domain() . url('bis/deal/waiting', ['id' => $data['id']]);
            $content = "o2o团购商品审核结果通知：请点击链接<a href='$url' target='_blank'>查看审核</a>查看审核结果。";
            \phpmailer\Email::send($to, $title, $content);
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }
    }
}
