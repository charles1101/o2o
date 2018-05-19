<?php
namespace app\admin\controller;

use think\Controller;

class Location extends Controller
{
    /**
     * 正常商户列表
     * @return mixed
     */
    public function index()
    {
        $bis = model('BisLocation')->getLocationByStatus();
        return $this->fetch('', [
            'bis' => $bis
        ]);
    }

    /**
     * 删除商户列表
     * @return mixed
     */
    public function dellist()
    {
        $bis = model('BisLocation')->getLocationByStatus(-1);
        return $this->fetch('', [
            'bis' => $bis,
        ]);
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
    public function status()
    {
        $data = input('get.');
        $validate = validate('Bis');
        if (!$validate->scene('status')->check($data)) {
            $this->error($validate->getError());
        }
        $res = model('BisLocation')->save(['status' => $data['status']], ['id' => $data['id']]);
        if ($res) {
            $bisLocation = model('BisLocation')->get($data['id']);
            $bisId = $bisLocation->bis_id;
            $bis = model('Bis')->get(['id'=>$bisId]);
            $to = $bis->email;
            $name = $bisLocation->name;
            $title = 'o2o门店下架通知';
            $content = "o2o门店下架通知：您的门店：$name，因违反相关法律已被下架。";
            \phpmailer\Email::send($to, $title, $content);
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }
    }

}
