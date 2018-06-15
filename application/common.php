<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * Ajax方式返回数据到客户端
 * @access protected
 * @param mixed $data 要返回的数据
 * @param String $type AJAX返回数据格式
 * @return void
 */
function ajaxReturn($data,$type='JSON') {

    switch (strtoupper($type)){
        case 'JSON' :
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode($data));
        break;
    }
}



/*文件夹操作*/

/**
 * 建立文件夹
 *
 * @param string $aimUrl
 * @return viod
 */
function createDir($aimUrl) {
    $aimUrl = str_replace('', '/', $aimUrl);
    $aimDir = '';
    $arr = explode('/', $aimUrl);
    $result = true;
    foreach ($arr as $str) {
        $aimDir .= $str . '/';
        if (!file_exists($aimDir)) {
            $result = mkdir($aimDir);
        }
    }
    return $result;
}

/**
 * 建立文件
 *
 * @param string $aimUrl
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function createFile($aimUrl, $overWrite = false) {
    if (file_exists($aimUrl) && $overWrite == false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite == true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
    createDir($aimDir);
    touch($aimUrl);
    return true;
}

/**
 * 移动文件夹
 *
 * @param string $oldDir
 * @param string $aimDir
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function moveDir($oldDir, $aimDir, $overWrite = false) {
    $aimDir = str_replace('', '/', $aimDir);
    $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
    $oldDir = str_replace('', '/', $oldDir);
    $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
    if (!is_dir($oldDir)) {
        return false;
    }
    if (!file_exists($aimDir)) {
        createDir($aimDir);
    }
    @ $dirHandle = opendir($oldDir);
    if (!$dirHandle) {
        return false;
    }
    while (false !== ($file = readdir($dirHandle))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($oldDir . $file)) {
            moveFile($oldDir . $file, $aimDir . $file, $overWrite);
        } else {
            moveDir($oldDir . $file, $aimDir . $file, $overWrite);
        }
    }
    closedir($dirHandle);
    return rmdir($oldDir);
}

/**
 * 移动文件
 *
 * @param string $fileUrl
 * @param string $aimUrl
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function moveFile($fileUrl, $aimUrl, $overWrite = false) {
    if (!file_exists($fileUrl)) {
        return false;
    }
    if (file_exists($aimUrl) && $overWrite = false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite = true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
    createDir($aimDir);
    rename($fileUrl, $aimUrl);
    return true;
}

/**
 * 删除文件夹
 *
 * @param string $aimDir
 * @return boolean
 */
function unlinkDir($aimDir) {
    $aimDir = str_replace('', '/', $aimDir);
    $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
    if (!is_dir($aimDir)) {
        return false;
    }
    $dirHandle = opendir($aimDir);
    while (false !== ($file = readdir($dirHandle))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($aimDir . $file)) {
            unlinkFile($aimDir . $file);
        } else {
            unlinkDir($aimDir . $file);
        }
    }
    closedir($dirHandle);
    return rmdir($aimDir);
}

/**
 * 删除文件
 *
 * @param string $aimUrl
 * @return boolean
 */
function unlinkFile($aimUrl) {
    if (file_exists($aimUrl)) {
        unlink($aimUrl);
        return true;
    } else {
        return false;
    }
}

/**
 * 复制文件夹
 *
 * @param string $oldDir
 * @param string $aimDir
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function copyDir($oldDir, $aimDir, $overWrite = false) {
    $aimDir = str_replace('', '/', $aimDir);
    $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
    $oldDir = str_replace('', '/', $oldDir);
    $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
    if (!is_dir($oldDir)) {
        return false;
    }
    if (!file_exists($aimDir)) {
        createDir($aimDir);
    }
    $dirHandle = opendir($oldDir);
    while (false !== ($file = readdir($dirHandle))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($oldDir . $file)) {
            copyFile($oldDir . $file, $aimDir . $file, $overWrite);
        } else {
            copyDir($oldDir . $file, $aimDir . $file, $overWrite);
        }
    }
    return closedir($dirHandle);
}

/**
 * 复制文件
 *
 * @param string $fileUrl
 * @param string $aimUrl
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function copyFile($fileUrl, $aimUrl, $overWrite = false) {
    if (!file_exists($fileUrl)) {
        return false;
    }
    if (file_exists($aimUrl) && $overWrite == false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite == true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
    createDir($aimDir);
    copy($fileUrl, $aimUrl);
    return true;
}



//curl post请求
/*
参数1：post数组数据
参数2：接口地址
    */
function Post($curlPost,$url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

//curl get请求
/*
参数1：接口地址
    */
function curl_get($url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

/*
xml转数组
    */
function xml_to_array($xml){
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
            $subxml= $matches[2][$i];
            $key = $matches[1][$i];
            if(preg_match( $reg, $subxml )){
                $arr[$key] = xml_to_array( $subxml );
            }else{
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

//生成随机串
function random($length = 6 , $numeric = 0) {
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if($numeric) {
        $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

function num_to_letter($num){
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
    //$chars[$num];
    return $chars[$num-1];
}

function num_to_chinese($num){
    $arr=array("零","一","二","三","四","五","六","七","八","九");
    return $arr[$num];
}


//照片上传
function base64toimg($base64_content,$path='./Uploads/1/image/'){
    if(!empty($base64_content)){
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_content, $result)){
            $path .= date('Y-m-d').'/';
            if (! file_exists($path)) {
                createDir($path);
            }
            $type = $result[2];
            $time = time().mt_rand(100,999);
            $filename = "$time.{$type}";
            $filepath = $path.$filename;
            $content =  base64_decode(str_replace($result[1], '', $base64_content));
            if (file_put_contents($filepath,$content)>0) {
                return substr($path,1).$filename;
            }else {
                return '';
            }
        }
        return $base64_content;
    }
}




