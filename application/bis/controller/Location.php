<?php
namespace app\bis\controller;

class Location extends Base
{
    public function index()
    {
        $bisId = $this->getLoginUser()->bis_id;
        $bis = model('BisLocation')->getLocationByBisId($bisId);
        return $this->fetch('', [
            'bis' => $bis,
        ]);
    }

    /**
     * 添加分店信息方法
     * @return mixed|void
     */
    public function add()
    {
        if (request()->isPost()) {
            //获取表单的值
            $data = input('post.');
            //校验分店信息
            $validate = validate('bis');
            if (!$validate->scene('add_location')->check($data)) {
                $this->error($validate->getError());
            }
            //获取经纬度
            $lngLat = \Map::getLngLat($data['address']);
            if (empty($lngLat) || $lngLat['status'] != 0 || $lngLat['result']['precise'] != 1) {
                $this->error('无法获取数据，或匹配的地址不精确');
            }
            //获取商户Id
            $bisId = $this->getLoginUser()->bis_id;
            //入库处理
            $locationData = [
                'bis_id' => $bisId,
                'name' => $data['name'],
                'logo' => $data['logo'],
                'tel' => $data['tel'],
                'contact' => $data['contact'],
                'category_id' => $data['category_id'],
                'category_path' => empty($data['se_category_id']) ? $data['category_id'] : $data['category_id'] . ',' . implode(',', $data['se_category_id']),
                'city_id' => $data['city_id'],
                'city_path' => empty($data['se_city_id']) ? $data['city_id'] : $data['city_id'] . ',' . $data['se_city_id'],
                'api_address' => $data['address'],
                'open_time' => $data['open_time'] . '-' . $data['open_time2'],
                'content' => empty($data['content']) ? '' : $data['content'],
                'is_main' => 0,//代表不是总店
                'xpoint' => empty($lngLat['result']['location']['lng']) ? '' : $lngLat['result']['location']['lng'],
                'ypoint' => empty($lngLat['result']['location']['lat']) ? '' : $lngLat['result']['location']['lat'],
            ];
            $locationId = model('BisLocation')->addLocation($locationData);
            if ($locationId) {
                return $this->success('新增门店成功');
            } else {
                return $this->error('新增门店失败');
            }
        } else {
            //获取一级城市
            $citys = model('City')->getNormalCitysByParentId();
            //获取一级分类
            $categorys = model('Category')->getNormalCategorys();
            return $this->fetch('', [
                'citys' => $citys,
                'categorys' => $categorys,
            ]);
        }
    }

    /**
     * 查看门店信息
     * @return mixed|void
     */
    public function detail()
    {
        $id = input('get.id');
        if (empty($id)) {
            return $this->error('id错误');
        }
        //获取一级城市
        $citys = model('City')->getNormalCitysByParentId();
        //获取一级分类
        $categorys = model('Category')->getNormalCategorys();
        //获取商户数据
        $locationData = model('BisLocation')->get($id);
        $seCategoryName = getSeCategoryName($locationData['category_path']);
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
            'locationData' => $locationData,
            'seCategoryName' => $seCategoryName,
        ]);
    }

    /**
     * 修改状态
     */
    public function status(){
        $data = input('get.');
        $validate = validate('Bis');
        if (!$validate->scene('status')->check($data)) {
            $this->error($validate->getError());
        }
        $res = model('BisLocation')->save(['status'=>$data['status']],['id'=>$data['id']]);
        if ($res) {
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }
    }
}
