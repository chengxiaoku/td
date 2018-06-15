<?php
namespace app\index\controller;
use think\View;
class Index extends Base
{
    public function index()
    {

        $view = new View();
        
        $view->assign('title', '控制台');
        $view->assign('controller',$this->getcontroller());

        return $view->fetch();
    }

}
