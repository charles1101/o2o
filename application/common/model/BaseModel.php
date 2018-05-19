<?php
namespace app\common\model;

use think\Model;

class BaseModel extends Model
{
    /**
     * 新增数据
     * @param $data
     * @return mixed
     */
    public function add($data)
    {
        $data['status'] = 0;
        $this->save($data);
        return $this->id;
    }

    /**
     * 通过id更新数据
     * @param $data
     * @param $id
     * @return mixed
     */
    public function updateById($data, $id)
    {
        //allowField是允许写入数据库的字段
        return $this->allowField(true)->save($data, ['id' => $id]);
    }
}