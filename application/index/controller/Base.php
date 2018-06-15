<?php
namespace app\index\controller;
use think\Config;
use Think\Loader;
use think\Db;
use think\Session;
use think\View;
use think\controller;
use think\response\Redirect;
use think\exception\HttpResponseException;
use think\Request;
class Base
{
    public function __construct()
    {
        //检测会话是否过期
        $now = time();
        $expire = Session::get("name.expire");
        if (is_null($expire)) {
            $expire = 0;
        }
        if ($now > $expire) {
            Session::clear();
            $this->redirect("Auth/index");
            exit;
        }
        //检测登陆
        if(!$this->is_login()){
            $this->redirect("Auth/index");
            exit;
        }
    }
    //判断是否登陆
    private function is_login(){
        return Session::has("name.nickname");
    }
    public function getDb($obj){
        return Db::name($obj);
    }
    /**自动验证
     * @param $obj
     * @return false|object
     */
    function validate($obj){
        return Loader::validate($obj);
    }

   
    public function ajaxSuccess($data=array(),$info="操作成功"){
        $result = array('success'=>true,'info'=>$info,'data'=>$data);
        ajaxReturn($result);
    }

    public function ajaxError($info="操作失败"){
        $result = array('success'=>false,'info'=>$info);
        ajaxReturn($result);
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
     * 获取当前 控制器名称
     * @return string
     */
    public function getcontroller(){
        return strtolower(Request::instance()->controller());
    }
}
