<?php
/**
 * Created by PhpStorm.
 * User: 程龙飞
 * Date: 2018/1/23
 * Time: 13:15
 */

namespace app\index\validate;
use think\Validate;
class Admin extends Validate
{

    protected $rule =   [
        'username'  => 'require|max:25',
        'nickname' => 'require|max:25',
    ];

    protected $message  =   [
        'username.require' => '账号不能为空',
        'username.max'     => '账号字符最多不能超过25个字符',
        'nickname.require' => '昵称不能为空',
        'nickname.max'     => '昵称字符最多不能超过25个字符',

    ];
}