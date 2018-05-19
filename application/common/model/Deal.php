<?php
namespace app\common\model;

class Deal extends BaseModel
{
    /**
     * 通过商户id获取团购商品信息
     * @param $bisId
     * @return \think\Paginator
     */
    public function getDealByBisId($bisId)
    {
        $data = [
            'status' => 1,
            'bis_id' => $bisId,
        ];
        $order = [
            'id' => 'desc',
        ];
        return $this->where($data)->order($order)->paginate();
    }

    /**
     * 通过状态获取团购商品信息
     * @param int $status
     * @return \think\Paginator
     */
    public function getDealByStatus($status = 1)
    {
        $data = [
            'status' => $status,
        ];
        $order = [
            'id' => 'desc',
        ];
        return $this->where($data)->order($order)->paginate();
    }

    /**
     * 获取正常团购商品信息
     * @param array $data
     * @return \think\Paginator
     */
    public function getNormalDeals($data = [])
    {
        $data['status'] = 1;
        $order = [
            'id' => 'desc',
        ];
        return model('Deal')->where($data)->order($order)->paginate();
    }

    /**
     * 通过分类id和城市id获取团购商品信息
     * @param $categoryId
     * @param $seCityId
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getDealByCategoryByCity($categoryId, $seCityId, $limit = 10)
    {
        $data = [
            'end_time' => ['gt', time()],
            'category_id' => $categoryId,
            'se_city_id' => $seCityId,
            'status' => 1,
        ];
        $order = [
            'id' => 'desc',
        ];
        $result = $this->where($data)->order($order);
        if ($limit) {
            $result = $result->limit($limit);
        }
        return $result->select();
    }

    public function getDealByCategoryByOrder($datas = [], $orders = [])
    {
        if (!empty($orders['order_sales'])) {
            $order['buy_count'] = 'desc';
        }
        if (!empty($orders['order_price'])) {
            $order['current_price'] = 'desc';
        }
        if (!empty($orders['order_time'])) {
            $order['update_time'] = 'desc';
        }
        $order['id'] = 'desc';
        $datas[] = 'status=1';
        $datas[] = 'end_time>' . time();
        $result = $this->where(implode(' AND ', $datas))->order($order)->paginate();
        return $result;
    }
}