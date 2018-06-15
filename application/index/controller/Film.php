<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/3/15
 * Time: 21:07
 */
namespace app\index\controller;
use think\Controller;
use think\View;
use think\Request;
use think\Config;
use think\Cache;
class Film extends Base
{
    public function index()
    {

        $view = new View();
        $film = $this->getDb('film');
        $pagenum = Config::get('pag');
        if (Request::instance()->isGet()) {
            $list = $film->order("add_time", 'desc')->paginate($pagenum);
            $find = '';
        }else{
            $find = input("post.find");
            $list = $film->where("film_title like '%".$find."%' OR to_star like '%".$find."%'")->order("add_time",'desc')->paginate($pagenum);

        }
        $view->assign('title', '电影管理');
        $view->assign('controller',$this->getcontroller());
        $view->assign('list',$list);
        $view->assign('find',$find);
        $view->assign('page', $list->render());
        return $view->fetch();
    }

    function add(){
        $data = input('post.');
        $this->isVal('Film', $data);
        if(empty($data['head_img'])){
            $this->ajaxError("封面不能为空");
        }else{
            $data['head_img'] = base64toimg($data['head_img']);
        }
        $data['add_time'] = time();
        unset($data['file']);
        $tr = $this->getDb('film')->insert($data);
        $tr ? $this->ajaxSuccess() : $this->ajaxError("添加失败");
    }
    function isVal($table,$data){
        $validate =  $this->validate($table);
        if(!$validate->check($data)){
            $this->ajaxError($validate->getError());
        }
    }

    /**
     * 调用了数据库的触发器 删除电影所关联的数据   且将热词的redis 删除
     */
    function del(){
        $id = input("id");
        $film = db('film');
        $conn = $this->my_conn_redis();
        if(is_numeric($id)){
            $val = $film->where("id=$id")->find();
            $film_title = trim($val['film_title'])."%".trim($val['id'])."%".trim($val['film_duration']);
            $conn->rm($film_title); //删除缓存
            $tr = $film->where("id=$id")->delete();
        }else{
            $arrid = explode(',',$id);
            foreach($arrid as $key => $val){
                $data = $film->where("id=$val")->find();
                $film_title = trim($data['film_title'])."%".$data($val['id'])."%".$data($val['film_duration']);
                $conn->rm($film_title); //删除缓存
            }
            $tr = $film->where('id','in',$arrid)->delete();
        }
        if(!$tr){
            $this->ajaxError();
        }else{
            $this->ajaxSuccess(array(),"删除成功");
        };
    }
    function update(){
        $film = db('film');
        if (Request::instance()->isGet()){
            $id = input("id");
            $data = $film->where("id=$id")->select();
            echo json_encode($data);
        }else{
            $data = input('post.');
            $id = $data['id'];
            unset($data['id']);
            unset($data['file']);
            $head_img = $film->field("head_img")->where("id=$id")->find();
            if(trim($head_img['head_img']) != $data['head_img']){
                $data['head_img'] = base64toimg($data['head_img']);
            }
            $this->isVal('film',$data);
            $tr =  model('Film')->save($data, array('id'=>$id));
            if($tr){
                $this->ajaxSuccess();
            }else{
                $this->ajaxError();
            }
        }

    }
    private function my_conn_redis(){
        $options = [
            'type'  => 'redis',//指定类型
            'password'=>'root',
        ];
        return Cache::init($options);
        // Cache::inc('listtest');
        //echo Cache::get("listtest");
    }
}