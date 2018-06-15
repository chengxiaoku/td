<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/1/26
 * Time: 14:08
 */
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\View;
use think\Request;
use think\Config;
class Password extends Base{
    function index(){
        if(Request::instance()->isGet()){
            $view = new View();
            $view->assign('controller',$this->getcontroller());
            $view->assign("title","修改管理员密码");
            return $view->fetch();
        }else{
            $newpassword1 = input("post.newpassword1");
            $newpassword2 = input("post.newpassword2");
            $oldpassword = input("post.oldpassword");
            if(empty($newpassword1) || empty($newpassword2) || empty($oldpassword)){
                $this->ajaxError("密码不能为空");
            }
            if($newpassword1 != $newpassword2){
                $this->ajaxError("两次新密码需一致");
            }

            $admin = $this->getDb("admin");
            $data = $admin->where("isadmin = 1")->find();
            if(!$data){
                $this->ajaxError("出现了一点问题");
            }
            if ($data['password'] == md5($oldpassword)){
                $bool = $admin->where("isadmin = 1")->update(['password' => md5($newpassword1)]);
                $bool ? $this->ajaxSuccess([],"超级管理员密码已修改") : $this->ajaxError("出现了一点问题");
            }else{
                $this->ajaxError("请输入正确的超级管理员密码");
            }
        }
    }
}