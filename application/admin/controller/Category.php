<?php
namespace app\admin\controller;

use think\Controller;

class Category extends Controller
{
    private $obj;

    /**
     * 初始化
     * @return \think\Model
     */
    public function _initialize()
    {
        return $this->obj = model('Category');
    }

    public function index()
    {
        $parentId = input('get.parent_id', 0, 'intval');
        $categorys = $this->obj->getAllCategorys($parentId);
        return $this->fetch('', [
            'categorys' => $categorys,
        ]);
    }

    /**
     * 添加页面
     * @return mixed
     */
    public function add()
    {
        $categorys = $this->obj->getNormalCategorys();
        return $this->fetch('', [
            'categorys' => $categorys,
        ]);
    }

    /**
     * 保存页面
     * @return mixed
     */
    public function save()
    {
        //print_r($_POST);
        //print_r(input('post.'));
        //print_r(request()->post());
        //做严格判断
        if (!request()->isPost()) {
            $this->error('请求失败');
        }
        $data = input('post.');
        $validate = validate('Category');
        if (!$validate->scene('add')->check($data)) {
            $this->error($validate->getError());
        }
        if (!empty($data['id'])) {
            return $this->update($data);
        }
        //把$data提交给model层
        $res = $this->obj->add($data);
        if ($res) {
            $this->success('新增成功');
        } else {
            $this->error('新增失败');
        }
    }

    /**
     * 编辑页面
     * @param int $id
     * @return mixed
     */
    public function edit($id = 0)
    {
        if (intval($id) < 1) {
            $this->error('参数不合法');
        }
        $category = $this->obj->get($id);
        $categorys = $this->obj->getNormalCategorys();
        return $this->fetch('', [
            'categorys' => $categorys,
            'category' => $category,
        ]);
    }

    /**
     * 更新页面
     * @param $data
     */
    public function update($data)
    {
        $res = $this->obj->save($data, ['id' => intval($data['id'])]); //save是think\Model中的方法，可以直接保存当前数据对象
        if ($res) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }

    /**
     * 排序逻辑
     * @param $id
     * @param $listorder
     */
    public function listorder($id,$listorder){
        $res = $this->obj->save(['listorder'=>$listorder],['id'=>$id]);
        if($res){
            $this->result($_SERVER['HTTP_REFERER'],1,'更新成功');
        }else{
            $this->result($_SERVER['HTTP_REFERER'],0,'更新失败');
        }
    }

    /**
     * 修改状态
     */
    public function status(){
        $data = input('get.');
        $validate = validate('Category');
        if (!$validate->scene('status')->check($data)) {
            $this->error($validate->getError());
        }
        $res = $this->obj->save(['status'=>$data['status']],['id'=>$data['id']]);
        if ($res) {
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }
    }
}
