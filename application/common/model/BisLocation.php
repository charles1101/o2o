<?php
namespace app\common\model;

class BisLocation extends BaseModel
{
    /**
     * 通过商户id获取商户信息
     * @param $bisId
     * @return \think\Paginator
     */
    public function getLocationByBisId($bisId)
    {
        $order = [
            'id' => 'desc',
        ];
        $data = [
            'bis_id' => $bisId,
            'status' => 1,
        ];
        return
            model('BisLocation')->where($data)->order($order)->paginate();
    }

    /**
     * 添加门店数据
     * @return mixed
     */
    public function addLocation($data)
    {
        $data['status'] = 1;
        $this->save($data);
        return $this->id;
    }

    /**
     * 通过状态获取门店信息
     * @param $status
     * @return \think\Paginator
     */
    public function getLocationByStatus($status=1)
    {
        $data = [
            'status' => $status,
        ];
        $order = [
            'id' => 'desc',
        ];
        return $this->where($data)->order($order)->paginate();
    }
}