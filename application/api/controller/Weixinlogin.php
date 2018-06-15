<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/3/24
 * Time: 14:44
 */
namespace app\api\controller;
use think\Db;
class Weixinlogin extends Base
{

    private $conn ;
    private $user_info = [
        1 =>['appid'=>'wxd5e0b442bb28aba0','appsecret'=>"47264b227bb312bce98d2b5b0983a6c7"],
        2 =>['appid'=>'wxdb126a114c4b6627','appsecret'=>"c353b4a8bd324a5908b4c43e207a5a05"],
        3 =>['appid'=>'wxf0a17caf981a4e6e','appsecret'=>"8be3e0212f068309c102fe09fc008f57"],
    ];
    function __construct()
    {
        $this->conn =$this->getDb("user");
    }

    function index(){
        $type = input("type");//根据用户的不同换取不同的APPid
        $type = intval($type);
        $user_data = $this->user_info[$type];
        $appid = $user_data['appid'];
        $appsecret = $user_data['appsecret'];
        $code = input("code");
        $json = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
        header("Content-Type: application/json");
        $json1 = file_get_contents($json);
        $arr = json_decode($json1,true);
        $openid = isset($arr['openid']) ? $arr['openid']:false;
        if(!$openid){
            $this->ajaxError("code 失效");
        }else{
            $userdata = $this->is_true_openid($openid);
            if($userdata){
                $this->ajaxSuccess(["code"=>1,"userid"=>$userdata['id']],'已经存在');
            }else{

                $data['user_id'] = $openid;
                $data['create_time'] = time();
                $tr1 = $this->conn->insertGetId($data);
                $tr1 ? $this->ajaxSuccess(["code" => 2,"userid"=>$tr1],"添加成功") : $this->ajaxError("添加失败");
            }
        }
    }
  

    /**
     * 检查openid是否存在
     */
    private function is_true_openid($openid){
        return $this->conn->where("user_id",$openid)->find();
    }

    public function set_user_data(){
        $id = input("user_id");
        if(!$this->conn->where("id",$id)->find()){
           $this->ajaxError("用户不存在");
        }
        $data['nickname'] = input("nickname");
        $data['head_img'] = input("head_img");
        $data['address'] = input("address");
        $data['constellation'] = input("constellation");
        $data['sex'] = input("sex");
        $data['age'] = input("age");
        $tr = $this->conn->where("id",$id)->update($data);
        $tr ? $this->ajaxSuccess() : $this->ajaxError();
    }

}