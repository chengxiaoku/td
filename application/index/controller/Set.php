<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/1/26
 * Time: 9:52
 */
namespace app\index\controller;
use think\Controller;
use think\View;
use think\Request;
use think\Config;
use think\Session;
class Set extends Base{

    function index(){
        if(Request::instance()->isGet()){
            $data = $this->getDb("set")->select();
            $arr = [];
            foreach ($data as $key => $val){
                $arr[$val['name']] = $val['value'];
            }
            $view = new view();
            $view->assign("set",$arr);
            $view->assign("title","系统信息设置");
            $view->assign('controller',$this->getcontroller());
            return $view->fetch();
        }else{
            $adminuser = input("post.adminuser");
            $comepanyuser = input("post.comepanyuser");
            Session::set('adminuser',$adminuser);
            Session::set('comepanyuser',$comepanyuser);
            $this->getDb("set")->where("name = 'adminuser'")->update(['value'=>$adminuser]);
            $this->getDb("set")->where("name = 'comepanyuser'")->update(['value'=>$comepanyuser]);
            $this->ajaxSuccess([],'保存成功');
        }

    }
}