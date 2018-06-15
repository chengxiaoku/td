<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/1/26
 * Time: 15:24
 */
return [
    //myconfig
    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    'base_url'=>'', // clf
    'DEFAULT_AJAX_RETURN' => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...
    'page' => 10,
    //会话过期时间
    'SESSION_EXPIRE' => 3600*24,
    
];