<?php
namespace app\admin\controller;

use think\Controller;

class Featured extends Controller
{
    public function index()
    {
        $data = input('get.');
        $sdata = [];
        if (!empty($data['type'])) {
            $sdata['type'] = $data['type'];
        }
        if (!empty($data['title'])) {
            $sdata['title'] = ['like', '%' . $data['title'] . '%'];
        }
        $featured = model('Featured')->getNormalFeatured($sdata);
        //推荐位类别
        $types = config('featured.featured_type');
        return $this->fetch('', [
            'types' => $types,
            'featured' => $featured,
            'type_id' => empty($data['type']) ? 0 : $data['type'],
            'title' => empty($data['title']) ? '' : $data['title'],
        ]);
    }

    /**
     * 添加推荐位
     * @return mixed
     */
    public function add()
    {
        if (request()->isPost()) {
            //获取添加推荐内容数据
            $data = input('post.');
            //数据验证
            $validate = validate('featured')->scene('add')->check($data);
            if (!$validate) {
                $this->error($validate->getError());
            }
            //插入数据
            $url = $data['url'];
            if (!preg_match('/(http:\/\/)|(https:\/\/)/i', $url)) {
                $url = 'http://' . $url;
            }
            $featured = [
                'title' => $data['title'],
                'image' => $data['image'],
                'type' => $data['type'],
                'url' => $url,
                'description' => $data['description'],
            ];
            $id = model('Featured')->add($featured);
            if ($id) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        }
        //推荐位类别
        $type = config('featured.featured_type');
        return $this->fetch('', [
            'type' => $type,
        ]);
    }

    /**
     * 修改状态
     */
    public function status()
    {
        $data = input('get.');
        $validate = validate('featured');
        if (!$validate->scene('status')->check($data)) {
            $this->error($validate->getError());
        }
        $res = model('Featured')->save(['status' => $data['status']], ['id' => $data['id']]);
        if ($res) {
            $this->success('状态更新成功');
        } else {
            $this->error('状态更新失败');
        }
    }


    public function edit($id = 0)
    {
        if(request()->isPost()){
            //获取添加推荐内容数据
            $data = input('post.');
            //数据验证
            $validate= validate('featured');
            if (!$validate->scene('add')->check($data)) {
                $this->error($validate->getError());
            }
            //插入数据
            if (!preg_match('/(http:\/\/)|(https:\/\/)/i', $data['url'])) {
                $data['url'] = 'http://' . $data['url'];
            }
            $id = model('Featured')->save($data,['id'=>intval($data['id'])]);
            if ($id) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        }
        $id = input('get.id');
        if (intval($id) < 1) {
            $this->error('非法参数');
        }
        $featured = model('featured')->getFeaturedById($id);
        $type = config('featured.featured_type');
        return $this->fetch('', [
            'type' => $type,
            'featured' => $featured,
        ]);
    }

}
