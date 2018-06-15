<?php
namespace app\api\controller;
use think\Db;
use think\Controller;
use think\View;
use think\Request;
use think\Config;
class Barrage extends Base
{
    function play(){
        $view = new View();
        return $view->fetch();
    }
}

