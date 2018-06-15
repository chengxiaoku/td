<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/3/17
 * Time: 16:14
 * 电影上映时间管理
 */
namespace app\index\controller;
use think\Controller;
use think\View;
use think\Request;
use think\Config;
use think\Session;
class Filmtime extends Base
{
    /**
     * @param $id
     * @return bool|string
     * @throws \Exception 后台添加弹幕
     */
    public function index($id)
    {
        $view = new View();
        $pagenum = Config::get('pag');
        $view->assign('title', '后台添加弹幕管理');
        if(!is_numeric($id)||empty($id)){
            return false;
        }
        $data = $this->getDb('film')->where("id = $id")->find();
        $view->assign("data",$data);
        $list = $this->getDb("barrage")->field("*,opening_time-8*60*60 as time")->where("film_id = $id AND is_people = 1")->order("create_time","desc")->paginate($pagenum);
        $view->assign('controller','film');
        $view->assign('list',$list);
        $view->assign('page', $list->render());

        //Session::set('content',"");
        //Session::set('content_time',"");
        $expire = Session::get("content");
        $content_time = Session::get("content_time");
        if(is_null($expire)){
            Session::set('content',"");
        }
        if(empty($content_time)){
            $str = "00:00:01";
            Session::set('content_time',$str);
            $content_time = $str;
        }
        $view->assign("content_mes",$expire);
        $view->assign("content_time",$content_time);
        return $view->fetch();
    }
    function add(){
        $data = input("post.");
        $data['send_id'] = 2;
        $data['is_people'] = 1;
        $data['create_time'] = time();

        Session::set('content',$data['content']);
        Session::set('content_time',$data['arr_time']);
        unset($data['arr_time']);
        $tr = $this->getDb("barrage")->insert($data);
        $tr ? $this->ajaxSuccess([],"添加成功") : $this->ajaxError("添加失败");
        
    }
    function del(){
        $id = input("id");
        $film = db('barrage');
        if(is_numeric($id)) {
            $tr = $film->where("id=$id")->delete();
        }

            $this->ajaxSuccess(array(),"删除成功");

    }
}