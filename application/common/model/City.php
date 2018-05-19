<?php
namespace app\common\model;

use think\Model;

class City extends Model
{
    /**
     * 通过父ID获取正常城市信息
     * @param int $parentId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNormalCitysByParentId($parentId = 0)
    {
        $data = [
            'parent_id' => $parentId,
            'status' => 1,
        ];
        $order = [
            'id' => 'desc',
        ];
        return $this->where($data)->order($order)->select();
    }

    /**
     * 获取正常城市信息
     * @param int $parentId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNormalCitys()
    {
        $data = [
            'parent_id' => ['neq', 0],
            'status' => 1,
        ];
        $order = [
            'id' => 'desc',
        ];
        return $this->where($data)->order($order)->limit(15)->select();
    }

    /**
     * 通过uname获取城市名
     * @param $uname
     * @return array|false|\PDOStatement|string|Model
     */
    public function getCityByUname($uname)
    {
        $data = [
            'uname' => $uname,
            'status' => 1
        ];
        return $this->where($data)->find();
    }

    /**
     * 通过id获取子城市名
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getSeCityName($id)
    {
        $data = [
            'id' => $id,
            'status' => 1
        ];
        return $this->where($data)->find();
    }
}