<?php
namespace app\common\model;

class Featured extends BaseModel
{
    public function getFeaturedById($id)
    {
        $data = [
            'id' => $id,
            'status' => ['neq', -1],
        ];
        return $this->where($data)->find();
    }

    public function getNormalFeatured($data = [])
    {
        $data['status'] = ['neq', -1];
        $order = [
            'id' => 'desc',
        ];
        return $this->where($data)->order($order)->paginate();
    }
}