<?php
namespace app\bis\controller;

class Index extends Base
{
    public function index()
    {
        $user = $this->getLoginUser();
        return $this->fetch('',[
            'user'=>$user
        ]);
    }
}
