<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/3/18
 * Time: 14:03
 */
//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("0.0.0.0", 8520,SWOOLE_BASE, SWOOLE_SOCK_TCP | SWOOLE_SSL);
//cweekend.cn 8520

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    //$ws->push($request->fd, "hello\n");
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    $data_arr = json_decode($frame->data,true);

    session_start();
    $time = isset($data_arr['time']) ? $data_arr['time'] :"";
    $filmid = isset($data_arr['filmid']) ? $data_arr['filmid'] :"";
    $user_session = &$_SESSION['user'];

    if(!empty($time) && !empty($filmid)){

        //重置time
        $user_session[$frame->fd] = json_encode(['time'=>$time,'filmid'=>$filmid]);

    }
    //$ws->push($frame->fd,$user_session[$frame->fd] );

    $str = json_decode( $user_session[$frame->fd],true);

    $_time = $str['time'];
    $_filmid =$str['filmid'];
    //clf
    //$ws->push($frame->fd,"自己的id时{$frame->fd},自己的time:".$_time."电影ID".$_filmid );

    //遍历所有连接，循环广播
    foreach($ws->connections as $fd){
        $flage = false;
        //如果不是自己的客户端则分发
        $str = json_decode( $user_session[$fd],true);
        $selftime = $str['time'];   //获取别人时间
        $selffilmid = $str['filmid'];   //获取别人电影ID
        $num = $selftime - $_time;  //算出是否开场时间在1分钟之内的两个客户端
        if($num < 60 && $num > -60 && $selffilmid == $_filmid){
            $flage = true;
        }
        if($frame->fd != $fd && $flage){
            //$ws->push($fd,"自己socket ID:{$fd},time:".$_time."电影ID".$_filmid.
            //    "发送者ID{$frame->fd}time:{$selftime}电影ID是".$selffilmid."误差是{$num}");
            $ws->push($fd, "{$data_arr['data']}");
        }
    }

});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    session_start();
    unset($_SESSION['user'][$fd]);
    echo "client-{$fd} is closed\n";
});
$ws->start();