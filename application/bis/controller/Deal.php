<?php
namespace app\bis\controller;

class Deal extends Base
{
    public function index()
    {
        $bisId = $this->getLoginUser()->bis_id;
        $deal = model('Deal')->getDealByBisId($bisId);
        return $this->fetch('', [
            'deal' => $deal
        ]);
    }

    /**
     * 添加分店信息方法
     * @return mixed|void
     */
    public function add()
    {
        $bisId = $this->getLoginUser()->bis_id;
        if (request()->isPost()) {
            //获取表单的值
            $data = input('post.');
            //数据校验
            $validate = validate('deal');
            if (!$validate->scene('deal')->check($data)) {
                $this->error($validate->getError());
            }
            //插入数据库
            $location = model('BisLocation')->get($data['location_ids'][0]);
            $deals = [
                'bis_id' => $bisId,
                'name' => $data['name'],
                'city_id' => $data['city_id'],
                'se_city_id' => $data['se_city_id'],
                'category_id' => $data['category_id'],
                'category_path' => empty($data['se_category_id']) ? $data['category_id'] : $data['category_id'] . ',' . implode(',', $data['se_category_id']),
                'location_ids' => empty($data['location_ids']) ? '' : implode(',', $data['location_ids']),
                'image' => $data['image'],
                'start_time' => strtotime($data['start_time']),
                'end_time' => strtotime($data['end_time']),
                'total_count' => $data['total_count'],
                'origin_price' => $data['origin_price'],
                'current_price' => $data['current_price'],
                'coupons_begin_time' => strtotime($data['coupons_begin_time']),
                'coupons_end_time' => strtotime($data['coupons_end_time']),
                'description' => $data['description'],
                'notes' => $data['notes'],
                'bis_account_id' => $this->getLoginUser()->id,
                'xpoint' => $location->xpoint,
                'ypoint' => $location->ypoint,
            ];
            $id = model('Deal')->add($deals);
            if ($id) {
                $url = request()->domain() . url('bis/deal/waiting', ['id' => $id]);
                $to = model('Bis')->get($bisId)->email;
                $title = 'o2o团购商品申请通知';
                $content = "您提交的申请需等待平台方审核，您可以通过点击链接<a href='$url' target='_blank'>查看审核</a>查看审核结果。";
                \phpmailer\Email::send($to, $title, $content);
                $this->success('提交团购商品成功，请耐心等待审核', url('deal/index'));
            } else {
                $this->error('提交失败');
            }
        } else {
            //获取一级城市
            $citys = model('City')->getNormalCitysByParentId();
            //获取一级分类
            $categorys = model('Category')->getNormalCategorys();
            $bisLocations = model('BisLocation')->getLocationByBisId($bisId);
            return $this->fetch('', [
                'citys' => $citys,
                'categorys' => $categorys,
                'bisLocations' => $bisLocations,
            ]);
        }
    }

    /**
     * 详细信息
     * @return mixed|void
     */
    public function detail()
    {
        $id = input('get.id');
        if (empty($id)) {
            return $this->error('id错误');
        }
        $dealData = model('Deal')->get($id);
        $bisId = $this->getLoginUser()->bis_id;
        //获取一级城市
        $citys = model('City')->getNormalCitysByParentId();
        //获取一级分类
        $categorys = model('Category')->getNormalCategorys();
        $bisLocations = model('BisLocation')->getLocationByBisId($bisId);
        $seCategoryName = getSeCategoryName($dealData['category_path']);
        $locationName = getLocationName($dealData['location_ids']);
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
            'bisLocations' => $bisLocations,
            'seCategoryName' => $seCategoryName,
            'locationName' => $locationName,
            'dealData' => $dealData,
        ]);
    }

    /**
     * 审核结果信息
     */
    public function waiting($id)
    {
        if(empty($id)){
            $this->error('error');
        }
        $detail = model('Deal')->get($id);
        return $this->fetch('', [
            'detail' => $detail,
        ]);
    }

    /**
     * 修改状态
     */
    public function status(){
        $data = input('get.');
        $validate = validate('deal');
        if (!$validate->scene('deal_status')->check($data)) {
            $this->error($validate->getError());
        }
        $res = model('Deal')->save(['status'=>$data['status']],['id'=>$data['id']]);
        if ($res) {
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }
    }
}
