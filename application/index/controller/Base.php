<?php
namespace app\index\controller;

use think\Controller;

class Base extends Controller
{
    //选取的城市信息
    public $city;

    //登录用户的信息
    public $account;

    public function _initialize()
    {
        $citys = model('City')->getNormalCitys();
        $this->city = $this->getCity($citys);
        $categoryArr = $this->getCategorys();

        $this->assign('citys', $citys);
        $this->assign('city', $this->city);
        $this->assign('user', $this->getLoginUser());
        $this->assign('categoryArr', $categoryArr);
        $this->assign('controller',strtolower(request()->controller()));
        $this->assign('title','o2o团购网');
    }

    /**
     * 获取默认城市
     * @param $citys
     * @return mixed
     */
    public function getCity($citys)
    {
        foreach ($citys as $city) {
            $cityArr = $city->toArray();
            if ($cityArr['is_default'] === 1) {
                $defaultuname = $cityArr['uname'];
                break;//终止foreach
            }
        }
        $defaultuname = isset($defaultuname) ? $defaultuname : 'beijing';
        if (session('o2o_city', '', 'o2o') && !input('get.city')) {
            $uname = session('o2o_city', '', 'o2o');
        } else {
            $uname = input('get.city', $defaultuname, 'trim');
            session('o2o_city', $uname, 'o2o');
        }
        return $this->city = model('city')->getCityByUname($uname);
    }

    /**
     * 获取登录信息
     * @return mixed
     */
    public function getLoginUser()
    {
        if (!$this->account) {
            $this->account = session('o2o_user', '', 'o2o');
        }
        return $this->account;
    }

    /**
     * 获取分类
     * @return array
     */
    public function getCategorys()
    {
        $categoryId = $seCategoryArr = $categoryArr = [];
        //获取一级分类
        $categorys = model('Category')->getRecommendCategorys();
        foreach ($categorys as $category) {
            $categoryId[] = $category->id;
        }
        //获取二级分类
        $seCategorys = model('Category')->getSeCategorys($categoryId);
        foreach ($seCategorys as $seCategory) {
            $seCategoryArr[$seCategory->parent_id][] = [
                'id' => $seCategory->id,
                'name' => $seCategory->name,
            ];
        }
        //拼接一级分类和二级分类，$categoryArr中[0]为一级分类，[1]为二级分类
        foreach ($categorys as $category) {
            $categoryArr[$category->id] = [$category->name, empty($seCategoryArr[$category->id]) ? '' : $seCategoryArr[$category->id]];
        }
        return $categoryArr;
    }

}
