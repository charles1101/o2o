<?php
namespace app\index\controller;
use think\Controller;

class Map extends Controller
{
    public function getMap($data){
        return \Map::getStaticImage($data);
    }
}