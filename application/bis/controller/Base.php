<?php
namespace app\bis\controller;

use think\Controller;

class Base extends Controller
{
    /**
     * 登录用户的信息
     * @var
     */
    public $account;

    /**
     * 根据用户登录状态初始化
     */
    public function _initialize()
    {
        $isLogin = $this->isLogin();
        if (!$isLogin) {
            return $this->redirect('login/index');
        }
    }

    /**
     * 判断用户是否登录
     * @return bool
     */
    public function isLogin()
    {
        $user = $this->getLoginUser();
        if ($user && $user->id) {
            return true;
        }
        return false;
    }

    /**
     * 获取登录信息
     * @return mixed
     */
    public function getLoginUser()
    {
        if (!$this->account) {
            $this->account = session('BisAccount', '', 'bis');
        }
        return $this->account;
    }
}
