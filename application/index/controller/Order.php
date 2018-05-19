<?php
namespace app\index\controller;

class Order extends Base
{
    public function index()
    {
        //dump(input('get.'));
        $user = $this->getLoginUser();
        if(!$user){
            $this->error('请先登录', 'user/login');
        }
        $id = input('get.id',0,'intval');
        $dealCount = input('get.count',0,'intval');
        $totalPrice = input('get.price',0,'intval');
        $deal = model('Deal')->find($id);
        if(!$deal||$deal->status!=1){
            $this->error('团购商品不存在');
        }
        $referer = $_SERVER['HTTP_REFERER'];
        if(empty($referer)){
            $this->error('非法操作');
        }
        //保存订单数据
        $data = [
            'deal_id' => $id,
            'deal_count' => $dealCount,
            'total_price' => $totalPrice,
            'out_trade_no'=>outTradeNo(),
            'user_id'=>$user->id,
            'username'=>$user->username,
            'referer'=>$referer,
        ];
        try{
            $orderId = model('Order')->add($data);
        }catch(\Exception $e){
            return $this->error('订单处理失败');
        }
        $this->redirect('pay/index',['id'=>$orderId]);
    }

    /**
     * 订单确认页
     * @return mixed
     */
    public function confirm()
    {
        if (!$this->getLoginUser()) {
            $this->error('请先登录', 'user/login');
        }
        $id = intval(input('get.id'));
        if (!$id) {
            $this->error('ID不合法');
        }
        $count = input('get.count', 1, 'intval');
        if (!$count) {
            $this->error('商品数量不合法');
        }
        //获取团购商品信息
        $deal = model('Deal')->get($id);
        if (!$deal || $deal->status != 1) {
            $this->error('团购商品不存在');
        }
        return $this->fetch('', [
            'controller' => 'pay',
            'deal' => $deal,
            'count' => $count,
        ]);
    }
}