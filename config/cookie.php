<?php
/*
 *  +----------------------------------------------------------------------
 *  | XinAdmin [ A Full stack framework ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2023~2024 http://xinadmin.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Apache License ( http://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: 小刘同学 <2302563948@qq.com>
 *  +----------------------------------------------------------------------
 */
// | Cookie设置
// +----------------------------------------------------------------------
return [
    // cookie 保存时间
    'expire' => 0,
    // cookie 保存路径
    'path' => '/',
    // cookie 有效域名
    'domain' => '',
    //  cookie 启用安全传输
    'secure' => false,
    // httponly设置
    'httponly' => false,
    // 是否使用 setcookie
    'setcookie' => true,
    // samesite 设置，支持 'strict' 'lax'
    'samesite' => '',
];
