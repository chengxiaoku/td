<?php
/*
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("0.0.0.0", 9520);

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});

//设置异步任务的工作进程数量
$serv->set(array('task_worker_num' => 4));

$serv->on('receive', function($serv, $fd, $from_id, $data) {
    //投递异步任务
    $task_id = $serv->task($data);
    //$serv->send($fd, "Server: ".$data);
});

//处理异步任务
$serv->on('task', function ($serv, $task_id, $from_id, $data) {
    echo "New AsyncTask[id=$task_id]".PHP_EOL;
    //返回任务执行的结果
    $serv->finish("$data -> OK");
});

//处理异步任务的结果
$serv->on('finish', function ($serv, $task_id, $data) {
    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});



//启动服务器
$serv->start();
*/
/*
//创建进程
function doProcess(swoole_process $worker){
    var_dump($worker);
    sleep(10);
}
$process = new swoole_process("doProcess");
$process->start();
*/

/*
$workers = []; //进程数组
$worker_num = 3; //创建进程的数据量
//创建启动进程
for ($i = 0; $i< $worker_num;$i++){
    $process = new swoole_process('doProcess'); //创建单独新进程,  参数二false（默认）,参数三true（默认）， false:管道进程通信
    $pid = $process->start();   //启动进程，并获取进程ID
    $workers[$pid] = $process; //存入进程数组
}
//创建进程执行函数
function doProcess(swoole_process $process){
    $process->write("PID:{$process->pid}"); //子进程写入信息
    echo "写入信息：$process->pid $process->callback";
}
//添加进程事件， 向每一个子进程添加需要执行的动作
foreach ($workers as $process){
    //添加
    swoole_event_add($process->pipe,function($pipe) use ($process)
    {
       $data = $process->read();
        echo '接收到:'.$data;
    });
}
*/

/*
 * 进程间通信
$workers = []; //进程数组
$worker_num = 2; //创建进程的数据量
//创建启动进程
for ($i = 0; $i< $worker_num;$i++){
    $process = new swoole_process('doProcess',false,false); //创建单独新进程,  参数二false（默认）,参数三true（默认）， false:管道进程通信
    $process->useQueue(); // 开启队列，类似于全局函数
    $pid = $process->start();   //启动进程，并获取进程ID
    $workers[$pid] = $process; //存入进程数组
}
//创建进程执行函数
function doProcess(swoole_process $process){
    $recv = $process->pop();
    echo "从主进程获取数据： $recv \n";
    sleep(5);
    $process->exit(0);  //退出
}
//主进程 向子进程添加数据
foreach ($workers as $pid => $process){
   $process->push("Hello 子进程 $pid \n");
}
//等待 子进程结束  回收资源
for($i = 0;$i<$worker_num;$i++){
    $ret = swoole_process::wait(); //等待执行完成
    $pid = $ret['pid'];
    unset($workers[$pid]);
    echo '子进程退出 '.$pid;
}
*/

//进程信号触发器   异步执行
//触发函数
/*swoole_process::signal(SIGALRM,function (){
    static $i = 0;
    echo "$i \n";
    $i++;
    if($i > 10 ){
        swoole_process::alarm(-1); //进行清除
    }
});
//定时信号
swoole_process::alarm(100 * 1000);
 */

/**
 * 创建锁机制
 */
//创建锁对象
/*$lock = new swoole_lock(SWOOLE_MUTEX); //互斥锁
echo "创建互斥锁";           //1
$lock->lock(); //开始锁定  主进程
if(pcntl_fork() > 0){
    sleep(1);
    $lock->unlock();//解锁
}else{
    echo '子进程等待锁\n';        //2
    $lock->lock();  //上锁
    echo '子进程获取锁';      //4
    $lock->unlock();    //释放锁
    exit('子进程退出');      //5
}
echo '主进程 释放锁';     //3
unset($lock);
sleep(1);
echo '子进程也退出';      //6
*/

//DNS 查询
swoole_async_dns_lookup("cweekend.cn",function ($host,$ip){
    echo $host,$ip;
});



