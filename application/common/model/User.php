<?php
namespace app\common\model;

class User extends BaseModel
{
    public function add($data = [])
    {
        if (!is_array($data)) {
            exception('传递的内容不是数组');
        }
        $data['status'] = 1;
        return $this->data($data)->allowField(true)->save();
    }

    public function getUserByUsername($username)
    {
        $data = [
            'username' => $username,
            'status' => 1,
        ];
        return $this->where($data)->find();
    }
}