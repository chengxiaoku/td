<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/1/26
 * Time: 14:40
 */
namespace app\index\controller;

use think\View;
use think\Controller;
use think\Request;
use think\Config;
class Admin extends Base{
    function index(){
        $list = $this->getDb('admin')->order('lasttime','desc')->select();
        $view = new View();
        $view->assign('title', '管理员管理');
        $view->assign('list',$list);
        $view->assign('controller',$this->getcontroller());
      
        return $view->fetch();
    }

    function add(){
        $data = input('post.');
        $data['createtime'] = time();
        $this->isVal('admin',$data);
        $data['password'] = md5(123456);
       
        unset($data['add']);
        $tr = $this->getDb('admin')->insert($data);
        $tr ? $this->ajaxSuccess() : $this->ajaxError();
    }
    function update(){
        if(Request::instance()->isGet()){
            $id = input('id');
            $data = $this->getDb('admin')->field("username,nickname,id,qx")->where('id',$id)->select();
            //$data[0]['qx'] = unserialize(base64_decode($data[0]['qx']));
            echo json_encode($data);
        }else{
            $data = input('post.');
            $id = $data['id'];
            unset($data['id']);
            $this->isVal('admin',$data);
            //$add = $data['update'];
            //unset($data['update']);
            //$data['qx'] = json_encode($add);
            $tr = $this->getDb('admin')->where('id',$id)->update($data);
            $tr ? $this->ajaxSuccess() : $this->ajaxError();
        }
    }
    function del(){
        $id = input('id');
        $tr = $this->getDb('admin')->where('id',$id)->delete();
        $tr ? $this->ajaxSuccess() : $this->ajaxError();
    }
    function isVal($table,$data){
        $validate =  $this->validate($table);
        if(!$validate->check($data)){
            $this->ajaxError($validate->getError());
        }
    }
}