<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/1/23
 * Time: 13:15
 */

namespace app\index\validate;
use think\Validate;
class Film extends Validate
{

    protected $rule =   [
        'film_title'  => 'require',
        'to_star' => 'require',
        'score' => 'number|between:0,100|require',
        'category' => 'require',
        'content' => 'require',
    ];

    protected $message  =   [
        'film_title.require' => '电影名称不能为空',
        'to_star.require' => '主演不能为空',
        'score.between' => '评分必须在0~10之间',
        'score.require' => '评分不能为空',
        'score.number' => '评分必须是数字',
        'category.require' => '类型不能为空',
        'content.require' => '简介不能为空',
    ];
    
}