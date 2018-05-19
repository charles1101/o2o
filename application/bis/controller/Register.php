<?php
namespace app\bis\controller;

use think\Controller;

class Register extends Controller
{
    public function index()
    {
        //获取一级城市
        $citys = model('City')->getNormalCitysByParentId();
        //获取一级分类
        $categorys = model('Category')->getNormalCategorys();
        return $this->fetch('', [
            'citys' => $citys,
            'categorys' => $categorys,
        ]);
    }

    /**
     * 添加申请入驻信息
     */
    public function add()
    {
        if (!request()->isPost()) {
            $this->error('请求错误');
        }
        //获取表单的值
        $data = input('post.');
        //校验基本信息
        $validate = validate('bis');
        if (!$validate->scene('add')->check($data)) {
            $this->error($validate->getError());
        }
        //获取经纬度
        $lngLat = \Map::getLngLat($data['address']);
        if (empty($lngLat) || $lngLat['status'] != 0 || $lngLat['result']['precise'] != 1) {
            $this->error('无法获取数据，或匹配的地址不精确');
        }
        //校验总店信息
        if (!$validate->scene('add_location')->check($data)) {
            $this->error($validate->getError());
        }
        //校验账户信息
        if (!$validate->scene('add_account')->check($data)) {
            $this->error($validate->getError());
        }

        //判断提交的用户是否存在
        $accountUser = model('BisAccount')->get(['username' => $data['username']]);
        if ($accountUser) {
            $this->error('该用户已存在，请重新分配账号');
        }

        //入库事务处理
        $modelBis = model('Bis');
        $modelBis->startTrans(); // 启动事务
        $modelLocation = model('BisLocation');
        $modelAccount = model('BisAccount');
        try {
            //商户基本信息入库
            $bisData = [
                'name' => $data['name'],
                'city_id' => $data['city_id'],
                'city_path' => empty($data['se_city_id']) ? $data['city_id'] : $data['city_id'] . ',' . $data['se_city_id'],
                'logo' => $data['logo'],
                'licence_logo' => $data['licence_logo'],
                'description' => empty($data['description']) ? '' : $data['description'],
                'bank_info' => $data['bank_info'],
                'bank_name' => $data['bank_name'],
                'bank_user' => $data['bank_user'],
                'faren' => $data['faren'],
                'faren_tel' => $data['faren_tel'],
                'email' => $data['email'],
            ];
            $bisId = $modelBis->add($bisData);
            //总店信息入库
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
                'open_time' => $data['open_time'].'-'.$data['open_time2'],
                'content' => empty($data['content']) ? '' : $data['content'],
                'is_main' => 1,//代表总店信息
                'xpoint' => empty($lngLat['result']['location']['lng']) ? '' : $lngLat['result']['location']['lng'],
                'ypoint' => empty($lngLat['result']['location']['lat']) ? '' : $lngLat['result']['location']['lat'],
            ];
            $modelLocation->add($locationData);
            //账户信息入库
            $data['code'] = mt_rand(100, 10000); //自动生成密码的加盐字符串
            $accountData = [
                'bis_id' => $bisId,
                'username' => $data['username'],
                'code' => $data['code'],
                'password' => md5($data['password'] . $data['code']),
                'is_main' => 1, //代表总店管理员
            ];
            $modelAccount->add($accountData);
            //提交事务
            $modelBis->commit();
            //发送邮件
            $url = request()->domain() . url('bis/register/waiting', ['id' => $bisId]);
            $to = $data['email'];
            $title = 'o2o入驻申请通知';
            $content = "您提交的申请需等待平台方审核，您可以通过点击链接<a href='$url' target='_blank'>查看审核</a>查看审核结果。";
            \phpmailer\Email::send($to, $title, $content);
        } catch (\Exception $e) {
            //事务回滚
            $modelBis->rollback();
            return $this->error('申请失败');
        }
        return $this->success('提交申请成功，请耐心等待审核', 'index/index/index');
    }

    /**
     * 审核结果信息
     */
    public function waiting($id)
    {
        if(empty($id)){
            $this->error('error');
        }
        $detail = model('Bis')->get($id);
        return $this->fetch('', [
            'detail' => $detail,
        ]);
    }
}