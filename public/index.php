<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

//加载xhprof断点
//xhprof_enable();

// 定义应用目录
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
//xhprof 语句
//$xhprof_data = xhprof_disable();
//include_once './xhprof_lib/utils/xhprof_lib.php';
//include_once './xhprof_lib/utils/xhprof_runs.php';
//$xhprof_runs = new XHProfRuns_Default();
//$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_test");

