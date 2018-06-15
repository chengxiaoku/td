<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/3/18
 * Time: 14:00
 */
namespace app\index\controller;
use think\Controller;
use think\View;
use think\Request;
use think\Config;
class Comment extends Base
{

    public function index()
    {
        $view = new View();
        $comment = $this->getDb('Comment');
        $pagenum = Config::get('pag');
        if (Request::instance()->isGet()) {
            $list = $comment->alias("a")
                ->field("a.id,a.content,a.create_time,a.up,b.nickname,c.film_title")
                ->join("td_user b" ,"a.send_id = b.id")
                ->join("td_film c","a.film_id = c.id")
                ->order("a.create_time", 'desc')
                ->paginate($pagenum);
            $find = '';
        }else{

            $find = input("post.find");
            $list = $comment->alias("a")
                ->field("a.id,a.content,a.create_time,a.up,b.nickname,c.film_title")
                ->join("td_user b" ,"a.send_id = b.id")
                ->join("td_film c","a.film_id = c.id")
                ->where("b.nickname like '%".$find."%' OR c.film_title like '%".$find."%'")
                ->order("a.create_time", 'desc')
                ->paginate($pagenum);
        }
        $view->assign('title', '评论管理');
        $view->assign('controller',$this->getcontroller());
        $view->assign('list',$list);
        $view->assign('find',$find);

        $view->assign('page', $list->render());
        return $view->fetch();
    }

    /**
     * @return string
     * @throws \Exception
     * 测试发送弹幕
     */
    public function add_barrage_mes(){
        $view = new View();
        $view->assign('title', '发送测试弹幕管理');
        $view->assign('controller',$this->getcontroller());
        return $view->fetch("add_test");
    }


    /**
     * 多选删除
     * 单选删除
     */
    function del(){
        $id = input("id");
        $film = db('comment');
        if(is_numeric($id)){
            $tr = $film->where("id=$id")->delete();
        }else{
            $arrid = explode(',',$id);
            $tr = $film->where('id','in',$arrid)->delete();
        }
        if(!$tr){
            $this->ajaxError();
        }else{
            $this->ajaxSuccess(array(),"删除成功");
        };
    }

}