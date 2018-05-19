<?php
namespace app\common\model;

class Bis extends BaseModel
{
    /**
     * 通过状态获取商户信息
     * @param int $status
     * @return \think\Paginator
     */
    public function getBisByStatus($status = 1)
    {
        $order = [
            'id'=>'desc',
        ];
        $data = [
            'status' => $status,
        ];
        return $this->where($data)->order($order)->paginate();
    }

    public function getBisById($id){
        $data = [
            'status' => 1,
            'id' => $id,
        ];
        return $this->where($data)->find();
    }
}