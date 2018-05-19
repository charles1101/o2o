<?php
namespace app\index\controller;

class Detail extends Base
{
    public function index()
    {
        $id = intval(input('get.id'));
        if (!$id) {
            $this->error('ID不合法');
        }
        //通过id获取团购商品详情
        $deal = model('Deal')->get($id);
        if (!$deal || $deal->status != 1) {
            $this->error('商品不存在或者商品状态错误');
        }
        $flag = 0;
        $timeData = '';
        if ($deal->start_time > time()) {
            $flag = 1;
            $time = $deal->start_time - time();
            $d = floor($time / (3600 * 24));
            if ($d) {
                $timeData .= $d . '天';
            }
            $h = floor($time % (3600 * 24) / 3600);
            if ($h) {
                $timeData .= $h . '小时';
            }
            $m = floor($time % 3600 / 60);
            if ($m) {
                $timeData .= $m . '分钟';
            }
            $s = floor($time % 60);
            if ($s) {
                $timeData .= $s . '秒';
            }
        }
        $locations = model('BisLocation')->getLocationByBisId($deal->bis_id);
        $bis = model('Bis')->getBisById($deal->bis_id);
        return $this->fetch('', [
            'title' => $deal->name,
            'deal' => $deal,
            'flag' => $flag,
            'timeData' => $timeData,
            'locations' => $locations,
            'bis' => $bis,
            'mapstr' => $locations[0]['xpoint'] . ',' . $locations[0]['ypoint'],
        ]);
    }
}