<?php
namespace app\common\model;

use think\Model;

class Category extends Model
{
    /**
     * 添加分类数据
     * @param $data
     * @return false|int
     */
    public function add($data)
    {
        $data['status'] = 1;
        return $this->save($data);
    }

    /**
     * 获取正常分类
     * @param int $parentId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNormalCategorys($parentId = 0)
    {
        $data = [
            'parent_id' => $parentId,
            'status' => 1,
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc',
        ];
        return $this->where($data)->order($order)->select();
    }

    /**
     * 获取所有分类
     * @param int $parentId
     * @return \think\Paginator
     */
    public function getAllCategorys($parentId = 0)
    {
        $data = [
            'status' => ['neq', -1],
            'parent_id' => $parentId,
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc',
        ];
        return $this->where($data)->order($order)->paginate();
    }

    /**
     * 获取限制数量的分类
     * @param int $parentId
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRecommendCategorys($parentId = 0, $limit = 5)
    {
        $data = [
            'parent_id' => $parentId,
            'status' => 1,
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc',
        ];
        $result = $this->where($data)->order($order);
        if ($limit) {
            $result = $result->limit($limit);
        }
        return $result->select();
    }

    /**
     * 获取所有子分类
     * @param array $categoryId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getSeCategorys($categoryId = [])
    {
        $data = [
            'parent_id' => ['in', implode(',', $categoryId)],
            'status' => 1,
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc',
        ];
        return $this->where($data)->order($order)->select();
    }

}