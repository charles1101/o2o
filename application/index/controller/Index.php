<?php
namespace app\index\controller;

class Index extends Base
{
    public function index()
    {
        //获取推荐位
        $featured = model('Featured')->getNormalFeatured();
        //通过分类id和城市id获取团购商品详情
        $deal = model('Deal')->getDealByCategoryByCity(5, $this->city->id);
        return $this->fetch('', [
            'featured' => $featured,
            'deal'=>$deal,
        ]);
    }

}
