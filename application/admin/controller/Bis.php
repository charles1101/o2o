<?php
namespace app\admin\controller;

use think\Controller;

class Bis extends Controller
{
    private $obj;

    public function _initialize()
    {
        $this->obj = model("Bis");
    }

    /**
     * 正常的商户列表
     * @return mixed
     */
    public function index()
    {
        $bis = $this->obj->getBisByStatus(1);
        return $this->fetch('', [
            'bis' => $bis,
        ]);
    }

    /**
     * 入驻申请列表
     * @return mixed
     */
    public function apply()
    {
        $bis = $this->obj->getBisByStatus(0);
        return $this->fetch('', [
            'bis' => $bis,
        ]);
    }

    /**
     * 商户详细信息
     * @return mixed|void
     */
    public function detail()
    {
        $id = input('get.id');
        if (empty($id)) {
            return $this->error('ID错误');
        }
        //获取一级城市的数据
        $citys = model('City')->getNormalCitysByParentId();
        //获取一级栏目的数据
        $categorys = model('Category')->getNormalCategorys();
        // 获取商户数据
        $bisData = model('Bis')->get($id);
        $locationData = model('BisLocation')->get(['bis_id' => $id, 'is_main' => 1]);
        $accountData = model('BisAccount')->get(['bis_id' => $id, 'is_main' => 1]);
        $categoryPath = $locationData->category_path;
        $seCategoryName = getSeCategoryName($categoryPath);
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
            'bisData' => $bisData,
            'locationData' => $locationData,
            'accountData' => $accountData,
            'seCategoryName' => $seCategoryName,
        ]);
    }

    // 修改状态
    public function status()
    {
        $data = input('get.');
        // 检验小伙伴自行完成
        /*$validate = validate('Bis');
        if(!$validate->scene('status')->check($data)) {
            $this->error($validate->getError());
        }*/

        $res = $this->obj->save(['status' => $data['status']], ['id' => $data['id']]);
        $location = model('BisLocation')->save(['status' => $data['status']], ['bis_id' => $data['id'], 'is_main' => 1]);
        $account = model('BisAccount')->save(['status' => $data['status']], ['bis_id' => $data['id'], 'is_main' => 1]);
        if ($res && $location && $account) {
            // 发送邮件
            // status 1  status 2  status -1
            // \phpmailer\Email::send($data['email'],$title, $content);
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }

    }

    /**
     * 删除的商户
     * @return mixed
     */
    public function dellist()
    {
        $bis = $this->obj->getBisByStatus(-1);
        return $this->fetch('', [
            'bis' => $bis,
        ]);
    }
}
