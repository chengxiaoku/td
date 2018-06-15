<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/6/5
 * Time: 17:10
 */

/*
$num = 1;
$id = swoole_timer_tick(2000, function ($timer_id) {

    $client = new swoole_client(SWOOLE_SOCK_TCP);

//连接到服务器
    if (!$client->connect("0.0.0.0", 9520, 0.5))
    {
        die("connect failed.");
    }
    //向服务器发送数据
    global  $num,$id;
    $client->send("hello world");
    $data = $client->recv();
    //从服务器接收数据
    echo $data;
    echo $num;
    $num++;
    if($num > 10){
        swoole_timer_clear($id);
        //关闭连接
        $client->close();
    }
});
*/

$db = new Swoole\MySQL;
$server = array(
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => '',
    'database' => 'test',
);

$db->connect($server, function ($db, $result) {
    $db->query("show tables", function (Swoole\MySQL $db, $result) {
        var_dump($result);
        $db->close();
    });
});
