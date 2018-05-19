<?php
namespace app\index\controller;

class Lists extends Base
{
    public function index()
    {
        $firstCategorys = [];
        $datas = [];
        //获取城市id
        $datas[] = 'se_city_id=' . $this->city->id;
        //获取一级分类
        $categorys = model('Category')->getNormalCategorys();
        foreach ($categorys as $category) {
            $firstCategorys[] = $category->id;
        }
        $id = input('id', 0, 'intval');
        //三种情况：一级分类 二级分类 全部分类
        if (in_array($id, $firstCategorys)) {//一级分类
            $categoryParentId = $id;
            $datas[] = 'category_id=' . $id;
        } elseif ($id) {//二级分类
            $seCategory = model('Category')->get($id);
            if (!$seCategory || $seCategory->status != 1) {
                $this->error('分类不存在');
            }
            $categoryParentId = $seCategory->parent_id;
            //查找有多个子分类的商品
            $datas[] = "find_in_set(" . $id . ",category_path)";
        } else {
            $categoryParentId = 0;
        }
        //获取二级分类
        if ($categoryParentId) {
            $seCategorys = model('Category')->getNormalCategorys($categoryParentId);
        } else {
            $seCategorys = '';
        }
        //获取用户选择的排序方式
        $orders = [];
        $order_sales = input('order_sales', '');
        $order_price = input('order_price', '');
        $order_time = input('order_time', '');
        if (!empty($order_sales)) {
            $orderFlag = 'order_sales';
            $orders['order_sales'] = $order_sales;
        } elseif (!empty($order_price)) {
            $orderFlag = 'order_price';
            $orders['order_price'] = $order_price;
        } elseif (!empty($order_time)) {
            $orderFlag = 'order_time';
            $orders['$order_time'] = $order_time;
        } else {
            $orderFlag = '';
        }
        //通过分类和排序方式获取团购商品数据
        $deals = Model('Deal')->getDealByCategoryByOrder($datas, $orders);
        return $this->fetch('', [
            'categorys' => $categorys,
            'seCategorys' => $seCategorys,
            'categoryParentId' => $categoryParentId,
            'id' => $id,
            'orderFlag' => $orderFlag,
            'deals' => $deals
        ]);
    }
}