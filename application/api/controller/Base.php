<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/3/24
 * Time: 15:54
 */
namespace app\api\controller;
use think\Db;
class Base
{
     function getDb($obj){
        return Db::name($obj);
    }

     function ajaxSuccess($data=array(),$info="操作成功"){
        $result = array('success'=>true,'info'=>$info,'data'=>$data);
        ajaxReturn($result);
    }

     function ajaxError($info="操作失败"){
        $result = array('success'=>false,'info'=>$info);
        ajaxReturn($result);
    }
}