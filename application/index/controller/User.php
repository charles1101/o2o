<?php
namespace app\index\controller;

class User extends Base
{
    public function register()
    {
        if (request()->isPost()) {
            //注册信息
            $data = input('post.');
            //数据验证
            $validate = validate('user');
            if (!$validate->scene('register')->check($data)) {
                $this->error($validate->getError());
            }
            if (!empty(model('user')->get(['username' => $data['username']]))) {
                $this->error('用户名已存在，请重新注册');
            }
            if ($data['password'] !== $data['repassword']) {
                $this->error('两次密码输入不一致');
            }
            if (!captcha_check($data['verifyCode'])) {
                $this->error('验证码错误，请重新输入');
            }
            $code = mt_rand(1000, 100000);
            $register = [
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => md5($data['password'] . $code),
                'code' => $code,
            ];
            try {
                $res = model('User')->add($register);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            if ($res) {
                return $this->success('注册成功', url('user/login'));
            } else {
                return $this->error('注册失败');
            }
        } else {
            return $this->fetch();
        }
    }

    public function login()
    {
        $referer = $_SERVER['HTTP_REFERER'];
        if (request()->isPost()) {
            $data = input('post.');
            //数据校验
            $validate = validate('user');
            if (!$validate->scene('login')->check($data)) {
                $this->error($validate->getError());
            }
            $user = model('User')->getUserByUsername($data['username']);
            if (!$user) {
                $this->error('用户不存在');
            }
            $password = $user->password;
            $code = $user->code;
            if (md5($data['password'] . $code) !== $password) {
                $this->error('密码不正确，请重新输入');
            }
            session('o2o_user', $user, 'o2o');
            model('User')->updateById(['last_login_time' => time(), 'last_login_ip' => request()->ip()], $user->id);
            return $this->success('登录成功',url($data['referer']));
        } else {
            $user = session('o2o_user', '', 'o2o');
            if ($user) {
                $this->redirect('index/index');
            }
        }
        return $this->fetch('', [
            'referer' => $referer,
        ]);
    }

    public function logout()
    {
        session(null, 'o2o');
        $this->redirect(url('user/login'));
    }
}
