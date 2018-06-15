<?php
namespace app\api\controller;
use think\Db;
use think\Cache;
class Index extends Base
{

    /**
     * 接收用户消息用户收到消息后进行websroket广播
     */
    public function add_barrage_mes(){

            //进行入库操作
            $data = input("post.");
            $data['create_time'] = time();
            $tr = $this->getDb('barrage')->insert($data);
            $tr ? $info = 1: $info =2;
            echo $info;
    }
    /**
     * 首页电影接口
     * release:1 上映  2：未上映
     */
    function get_film(){
        $release = input("release");
        $userid = input("userid");
        $page = input("page",1);
        $page_num = input("page_num",5);
        $tr = $this->getDb('film')->alias("a")
            ->field("a.*,ifnull(b.id,-1) as liketype")
            ->join("td_would_like b","b.film_id = a.id AND b.user_id = $userid","left")
            ->where("a.release =$release")
            ->order("a.add_time desc")
            ->page($page,$page_num)
            ->select();
        $this->ajaxSuccess($tr);
    }
    /**
     *opening_time_start  opening_time_end  拉取电影的时间范围
     * film_id 电影id
     * @param int $barrage_max  每秒最大弹幕拉取量
     */
    function get_barrage($opening_time_start,$opening_time_end,$film_id,$barrage_max=15){
//        $tr = $this->getDb('barrage')
//            ->field("content,opening_time")
//            ->where("film_id = $film_id AND opening_time > $opening_time_start AND opening_time < $opening_time_end")
//            ->order("create_time desc")
//            ->select();
//        echo $this->getDb('barrage')->getLastSql();

        $tr = $this->getDb("barrage")
            ->query("select a.content as text,opening_time+0 as time
from  
(  
select t1.*,(select count(*)+1 from td_barrage where opening_time=t1.opening_time and id<t1.id ) as group_id  
from td_barrage t1 
) a  
where a.group_id<=$barrage_max AND a.opening_time > $opening_time_start AND  a.opening_time < $opening_time_end AND film_id = $film_id ORDER BY a.opening_time*1 asc ");  //排序时出现例如9比88大的情况，是由于字符串对比时对比首数字，故转化类型为整形，乘1 将字符串转化为数字

        $this->ajaxSuccess($tr);
    }


    /**
     * 保存评论接口
     */
    function save_comment(){
        $data = input("post.");
        $data['create_time'] = time();
        $comment = $this->getDb("comment");
        $send_id = $data['send_id'];
        $film_id = $data['film_id'];
        $tr = false;
        if(!$comment->where("send_id = $send_id AND film_id = $film_id")->update($data)){
            $tr = $comment->insert($data);
        }else{
            $tr = true;
        }

        $tr ? $this->ajaxSuccess() : $this->ajaxError("添加失败");
    }
    /**
     * 获取电影评论接口
     */
    function get_comment(){
        $film_id = input("film_id");
        $user_id = input("user_id");
        $type = input("type");

        if($type == 1){
            $string = "create_time";
        }else if($type == 2){
            $string = "up";
        }
        $data = $this->getDb("comment")->alias("a")
            ->field("a.*,FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H:%i:%s') as time,b.nickname,b.constellation,b.sex,b.head_img,b.age,IFNULL(c.type,2)as type")
            ->join("td_user b","b.id = a.send_id")
            ->join("td_like c","c.user_id = $user_id AND c.comment_id = a.id","left")
            ->where("a.film_id",$film_id)
            ->order("a.$string","DESC")
            ->page(input("page"),input("page_num"))
            ->select();
        $this->ajaxSuccess($data);
    }
    /**
     * 点赞接口
     * user_id 点赞人ID   comment_id 评论ID
     */
    function like($user_id,$comment_id){
        $like = $this->getDb("like");
        $tr = $like->where("user_id=$user_id AND comment_id = $comment_id")->find();
        $data = empty($tr) ? 1 :2;
        if($data == 1){
            $insert_data['user_id']= $user_id;
            $insert_data['comment_id'] = $comment_id;
            $insert_data['type'] = 1;
            $insert_data['create_time'] = time();
            $like->insert($insert_data);
            $this->getDb("comment")->where("id = $comment_id")->setInc('up',1);
            $this->ajaxSuccess(["info"=>1],"点赞成功");//点赞
        }else{
            $id = $tr['id'];
            $type = $tr['type'];
            $time = time();
            if($type == 1){ //取消点赞

                $like->where("id = $id")->update(['type' => 2]);
                $this->getDb("comment")->where("id = $comment_id")->setDec('up',1);
                $this->ajaxSuccess(["info"=>2],"取消点赞成功");

            }else if($type == 2){
                $like->where("id =$id")->update(['type' => 1,"create_time" => $time,"is_look"=>1]);
                $this->getDb("comment")->where("id = $comment_id")->setInc('up',1);
                $this->ajaxSuccess(["info"=>1],"点赞成功");
            }
        }
    }

    /**
     * @param $user_id   用户ID
     * @param $page 当前页码
     * @param $page_num  每页数量
     * 获取我的弹幕接口
     */
    function my_barrage($user_id,$page,$page_num){
        $data = $this->getDb("barrage")->alias("a")
            ->field("a.content,FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H:%i:%s') as time,b.film_title")
            ->join("td_film b","b.id = a.film_id")
            ->order("a.create_time","desc")
            ->where("a.send_id",$user_id)
            ->page($page,$page_num)
            ->select();
        $this->ajaxSuccess($data);
        
    }

    /**获取用户历史评论
     * @param $user_id  用户ID
     * @param $page  页码
     * @param $page_num  每页数量
     */
    function my_comment($user_id,$page,$page_num){
        $data = $this->getDb("comment")->alias("a")
            ->field("a.content,FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H:%i:%s') as time,a.up,b.film_title")
            ->join("td_film b","b.id = a.film_id")
            ->where("a.send_id",$user_id)
            ->order("a.create_time","DESC")
            ->page($page,$page_num)
            ->select();
        $this->ajaxSuccess($data);
    }

    /**
     * 获取收到的未读赞数量
     * $type 有值表示不需要json 需要未读数量
     */
    function get_up_num($user_id,$type = false){
        //$data = $this->getDb("comment")->field("sum(up) sum_up")->where("send_id = $user_id")->find();
        $data = $this->getDb("like")
            ->alias("a")
            ->join("td_comment b","b.id = a.comment_id")
            ->field("count(a.is_look) as num")
            ->where("b.send_id = $user_id and a.is_look = 1 and a.type = 1")
            ->find();
        if(empty($data)){
            $data = 0;
        }
        if($type){
            return $data;
        }else{
            $this->ajaxSuccess($data);
        }

    }
    /**
     * 获取收到的赞详情列表页
     */
    function get_up($user_id,$page=1,$page_num=10){
        //$json = $this->get_up_num($user_id,true); //获取未读数量

        $data = $this->getDb("like")
                ->alias("a")
                ->field("b.content,a.is_look,FROM_UNIXTIME(a.create_time,'%Y-%m-%d %H:%i:%s') as create_time,c.nickname,c.head_img user_img,d.film_title,d.id,d.head_img film_img")

                ->join("td_comment b","b.id=a.comment_id")
                ->join("td_user c","a.user_id = c .id","left")
                ->join("td_film d","b.film_id = d.id","left")
                ->where("a.type = 1 AND b.send_id = $user_id")
                ->order("a.is_look asc,a.create_time desc")
                ->page($page,$page_num)
                ->select();
       // $data['up_num'] = $json["num"];
        $this->update_like($user_id);
        $this->ajaxSuccess($data);
    }

    /**
     * 修改用户，将未读的赞修改为已读
     */
    private function update_like($user_id){
        $this->getDb("like")->alias("a")
            ->join("td_comment b","b.id=a.comment_id")
            ->where("b.send_id=$user_id")->update(["is_look"=>2]);
    }

    /**
     * 观看历史
     */
    function get_history($user_id,$page=1,$page_num=10){
        $data = $this->getDb("history")->alias("a")
            ->field("c.id,c.max,c.film_duration,c.head_img,d.score,d.content,c.film_title,c.type")
            ->join("td_user b","a.user_id = b.id","left")
            ->join("td_film c","a.film_id = c.id","left")
            ->join("td_comment d","c.id = d.film_id AND d.send_id = $user_id","left")
            ->where("a.user_id = $user_id ")
            ->order("a.create_time","desc")
            ->page($page,$page_num)
            ->select();
        $this->ajaxSuccess($data);
    }
    /**
     * 记录观看历史
     */
    function set_history($user_id,$film_id){
        $data['user_id'] = $user_id;
        $data['film_id'] = $film_id;
        $history = $this->getDb("history");
        $data['create_time'] = time();
        if(!$history->where("user_id = $user_id AND film_id = $film_id")->update(["create_time"=>time()])) {
            $tr = $history->insert($data);
            $tr ? $this->ajaxSuccess() : $this->ajaxError();
        }else{
            $this->ajaxSuccess();
        }

    }
    /**
     * 我想看接口
     * 待映
     */
    function would_like($user_id,$film_id){
        $data['user_id'] = $user_id;
        $data['film_id'] = $film_id;
        $data['create_time'] = time();
        $tr = $this->getDb("would_like")->insert($data);
        $tr ? $this->ajaxSuccess() : $this->ajaxError();
    }

    /**
     * 搜索接口
     */
    function seach($seach,$userid){

        $tr = $this->getDb('film')->alias("a")
            ->field("a.*,ifnull(b.id,-1) as liketype")
            ->join("td_would_like b","b.film_id = a.id AND b.user_id = $userid","left")
            ->where("film_title like '%".$seach."%'")
            ->order("a.add_time desc")
            ->select();
        $conn = $this->my_conn_redis();
        if(!empty($tr)){
            foreach ($tr as $key => $val){
                $film_title = trim($val['film_title'])."%".trim($val['id'])."%".trim($val['film_duration']);
                if(!$conn->has($film_title)){
                    $conn->set($film_title,1,0);
                }else{
                    $conn->inc($film_title);
                }
            }
        }
        exit(json_encode($tr));
    }

    /**
     * 显示搜索   根据热度显示前5名
     */
    function show_seach(){
        $conn = $this->my_conn_redis();

        $data = $conn->handler()->keys('*');

        $arr = [];
        foreach ($data as $key=>$val){
            $arr[$val] = $conn->get($val);
        }
        arsort($arr);
        $this->ajaxSuccess(array_slice($arr,0,5));
    }
    /**
     * 获取用户信息
     */
    function get_user_data($userid){
        $data = $this->getDb("user")->where("id",$userid)->find();
        $data ? $this->ajaxSuccess($data) : $this->ajaxError();
    }

    /**
     * redis
     */
    function test(){
        phpinfo();
    }
    private function my_conn_redis(){
        $options = [
            'type'  => 'redis',//指定类型
            'password'=>'root',
        ];
        return Cache::init($options);
        //Cache::inc('listtest');
        //echo Cache::get("listtest");
    }

    /**
     * 是否评论过
     */
    function is_comment($send_id,$film_id){
        $tr = $this->getDb("comment")->where("send_id = $send_id AND film_id = $film_id")->find();
        $tr ? $this->ajaxSuccess($tr) : $this->ajaxError();
    }

    function user_update_info($id,$sex,$age,$constellation){
        $user = $this->getDb("user");
        $userdata = $user->where("id=$id")->find();
        if($userdata){
            $is_update = $userdata['is_update'];
            if($is_update == 0){
                $data['sex'] = $sex;
                $data['age'] = $age;
                $data['constellation'] = $constellation;
                $data['is_update'] = 1;
                $tr = $user->where("id=$id")->update($data);
                $tr ? $this->ajaxSuccess():$this->ajaxError();
            }else{
                $this->ajaxError("你已经修改过信息");
            }
        }

    }

    /**
     *清除所有redis
     */
    function redis_rm_(){
        $conn = $this->my_conn_redis();
        $conn->clear();
    }

    /**删库跑路
     * @param $key
     * 禁止调用
     */
    function clear_data($key = null){
        if(empty($key) || $key != "clf"){
            exit("请输入正确的key");
        }else{
            $tr = $this->getDb("admin")->query("drop database xcx");
            echo $tr ? "已清空": "失败";
        }
    }

}

