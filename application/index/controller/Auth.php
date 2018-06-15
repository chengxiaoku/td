<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/1/25
 * Time: 19:33
 */
namespace app\index\controller;
use think\Controller;
use think\View;
use think\Request;
use think\Db;
use think\captcha\Captcha;
use think\Session;
use think\Config;
use think\response\Redirect;
use think\exception\HttpResponseException;
class Auth
{
    function index(){
        if(Request::instance()->isGet()){
            $this->getmes();
            return view('login');
        }else{
            $username = input("post.username");
            $password = input("post.password");
            $captcha = input("post.captcha");
            if(empty($username) || empty($password) || empty($captcha)){
                $this->ajaxError("请输入内容");
            }else{
                if(!captcha_check($captcha)){
                    $this->ajaxError("验证码不正确");
                }else{
                    $this->is_adminuser($username, $password);
                }
            }
        }
    }

    /**
     * 判断账号密码
     * @param $username
     * @param $password
     */
    function is_adminuser($username,$password){
        $password = md5($password);
        $data = Db::name('admin')->where("username = '$username' AND password = '$password'")->find();
        if($data){
            Session::set('name.nickname',$data['nickname']);
            Session::set('name.id',$data['id']);
            Session::set('name.qx',$data['qx']);
            $session_expire = Config::get('SESSION_EXPIRE');
            Session::set('name.expire',time() + $session_expire);
            $this->ajaxSuccess([],"登陆成功");
        }else{
            $this->ajaxError("账号密码不正确");
        }
    }
    
    function getCaptcha(){
        $config =    [
            // 验证码字体大小
            'imageH'    =>    50,
            // 验证码位数
            'imageW'      =>    180,
            'length' => 4,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

    public function ajaxSuccess($data=array(),$info="操作成功"){
        $result = array('success'=>true,'info'=>$info,'data'=>$data);
        ajaxReturn($result);
    }

    public function ajaxError($info="操作失败"){
        $result = array('success'=>false,'info'=>$info);
        ajaxReturn($result);
    }
    function loginout(){
        Session::clear();
        $this->redirect("Auth/index");
    }
    /**
     * URL 重定向
     * @access protected
     * @param string    $url    跳转的 URL 表达式
     * @param array|int $params 其它 URL 参数
     * @param int       $code   http code
     * @param array     $with   隐式传参
     * @return void
     * @throws HttpResponseException
     */
    protected function redirect($url, $params = [], $code = 302, $with = [])
    {
        if (is_integer($params)) {
            $code   = $params;
            $params = [];
        }

        $response = new Redirect($url);
        $response->code($code)->params($params)->with($with);
        throw new HttpResponseException($response);
    }
    /**
     * 获取系统信息 并 保存
     */
    public function getmes(){
        $data = Db::name('set')->select();
        foreach ($data as $key => $val){
            Session::set($val['name'],$val['value']);
        }
    }
}