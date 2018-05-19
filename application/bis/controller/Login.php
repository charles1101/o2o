<?php
namespace app\bis\controller;

use think\Controller;

class Login extends Controller
{
    public function index()
    {
        if (request()->isPost()) {
            //登录逻辑
            //获取相关数据
            $data = input('post.');
            //验证用户登录信息
            $validate = validate('Bis');
            if (!$validate->scene('login')->check($data)) {
                $this->error($validate->getError());
            }
            //通过用户名判断用户相关信息
            $ret = model('BisAccount')->get(['username' => $data['username']]);
            if (!$ret && $ret->status != 1) {
                $this->error('用户不存在或者用户未通过审核');
            }
            if ($ret->password != md5($data['password'] . $ret->code)) {
                $this->error('密码不正确，请重新输入');
            }
            model('BisAccount')->updateById(['last_login_time' => time(), 'last_login_ip' => request()->ip()], $ret->id);
            //保存用户信息 bis是作用域
            session('BisAccount', $ret, 'bis');
            $this->success('登录成功', url('index/index'));
        } else {
            //获取session
            $account = session('BisAccount', '', 'bis');
            if ($account && $account->id) {
                $this->redirect('index/index');
            }
            return $this->fetch();
        }
    }

    public function logout()
    {
        //清除session
        session(null, 'bis');
        $this->redirect('login/index');
    }
}